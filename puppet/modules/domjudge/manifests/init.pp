# == Class: domjudge
#
# Sets up everything for DOMjudge
#
class domjudge {
	exec {
		'configure domjudge':
			cwd     => '/vagrant',
			command => 'make maintainer-conf CONFIGURE_FLAGS="--with-domjudge-user=vagrant"',
			path    => ['/bin', '/usr/bin'];

		'install domjudge':
			cwd     => '/vagrant',
			command => 'make maintainer-install',
			path    => ['/usr/local/sbin','/usr/local/bin','/usr/sbin','/usr/bin','/sbin','/bin'],
			require => Exec['configure domjudge'];

		'generate database passwords':
			cwd     => '/vagrant',
			command => '/vagrant/sql/dj-setup-database genpass',
			creates => '/vagrant/etc/dbpasswords.secret',
			require => Exec['install domjudge'];

		'setup initial database structure':
			cwd     => '/vagrant',
			path    => ['/bin', '/usr/bin'],
			command => '/vagrant/sql/dj-setup-database -uroot -proot install',
			unless  => 'echo "show databases" | mysql -uroot -proot | grep domjudge 2> /dev/null',
			require => Exec['generate database passwords'];

		'upgrade database':
			cwd     => '/vagrant',
			path    => ['/bin', '/usr/bin'],
			command => '/vagrant/sql/dj-setup-database -uroot -proot upgrade',
			require => Exec['setup initial database structure'];
	}

	file {
		'/etc/nginx/sites-enabled/default':
			ensure => absent,
			before => File['/etc/nginx/sites-available/domjudge'];

		'/etc/nginx/sites-available/domjudge':
			owner => 'www-data',
			group => 'www-data',
			mode  => '0644',
			source => 'puppet:///modules/domjudge/domjudge-nginx';

		'/etc/nginx/sites-enabled/domjudge':
			ensure => symlink,
			target => '/etc/nginx/sites-available/domjudge',
			require => File['/etc/nginx/sites-available/domjudge'],
			notify => Exec['restart nginx'];

		'/etc/sudoers.d/domjudge':
			source  => '/vagrant/etc/sudoers-domjudge',
			owner   => 'root',
			group   => 'root',
			mode    => '0440',
			require => User['domjudge-run'];

		'/home/vagrant/bin':
			ensure => directory,
			owner  => 'vagrant',
			group  => 'vagrant',
			mode   => '0755';

		'/home/vagrant/bin/judgedaemon':
			source  => 'puppet:///modules/domjudge/judgedaemon',
			owner   => 'vagrant',
			group   => 'vagrant',
			mode    => '755',
			require => File['/home/vagrant/bin'];
	}

	exec {
		'restart nginx':
			command => '/etc/init.d/nginx restart';

		'fix restapi server':
			cwd     => '/vagrant',
			path    => ['/bin', '/usr/bin'],
			command => 'sed -i \'s/localhost\/domjudge/localhost/g\' etc/restapi.secret',
			onlyif  => 'grep domjudge etc/restapi.secret 2>/dev/null',
			require => Exec['install domjudge'];
	}

	user {
		'domjudge-run':
			ensure  => present,
			gid     => 'nogroup',
			home    => '/nonexistent',
			shell   => '/bin/false',
			require => Exec['install domjudge'];
	}
}
