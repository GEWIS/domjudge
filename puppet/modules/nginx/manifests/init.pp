# == Class: nginx
#
# Installs packages for nginx, and sets config files.
#
class nginx {
	package { ['nginx']:
		ensure => present;
	}

	package { ['apache2']:
		ensure => absent,
		notify => Service['nginx'];
	}

	service { 'nginx':
		ensure  => running,
		require => Package['nginx'];
	}
}
