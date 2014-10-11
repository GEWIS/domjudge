# == Class: mysql
#
# Installs MySQL server, sets config file, and loads database
#
class mysql {
	package { ['mysql-server']:
		ensure => present;
	}

	service { 'mysql':
		ensure  => running,
		require => Package['mysql-server'];
	}

	exec { 'set-mysql-password':
		unless  => 'mysqladmin -uroot -proot status',
		command => "mysqladmin -uroot password root",
		path    => ['/bin', '/usr/bin'],
		require => Service['mysql'];
	}

	file { '/etc/mysql/conf.d/domjudge.cnf':
			owner => 'mysql',
			group => 'mysql',
			mode  => '0644',
			source => 'puppet:///modules/mysql/domjudge.cnf',
			notify => Service['mysql'];
	}
}
