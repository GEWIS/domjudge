# == Class: php
#
# Installs PHP5 and necessary modules. Sets config files.
#
class php {
	package { [
		'php5',
		'php5-cli',
		'php5-fpm',
		'php5-curl',
		'php5-gd',
		'php5-imagick',
		'php5-mcrypt',
		'php5-mysql',
		'php5-xdebug'
		]:
		ensure => present;
	}

	file {
		'/etc/php5/fpm':
			ensure => directory,
			before => File ['/etc/php5/fpm/pool.d'];

		'/etc/php5/fpm/pool.d':
			ensure => directory,
			before => File ['/etc/php5/fpm/pool.d/vagrant.conf'];

		'/etc/php5/fpm/pool.d/vagrant.conf':
			source => 'puppet:///modules/php/fpm-vagrant.conf',
			require => Package['php5-fpm'],
			notify => Service['php5-fpm'];

		'/etc/php5/fpm/pool.d/www.conf':
			ensure => absent,
			require => Package['php5-fpm'],
			notify => Service['php5-fpm'];
	}

	service { 'php5-fpm':
		ensure  => running,
		require => Package['php5-fpm'],
		hasrestart => true
	}
}
