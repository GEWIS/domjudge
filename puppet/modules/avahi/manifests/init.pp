# == Class: avahi
#
# Installs avahi-daemon, sets config file, and starts it
#
class avahi {
	package { ['avahi-daemon']:
		ensure => present;
	}

	service { 'avahi-daemon':
		ensure  => running,
		require => Package['avahi-daemon'];
	}
}
