# == Class: baseconfig
#
# Performs initial configuration tasks for all Vagrant boxes.
#
class baseconfig {
	exec { 'apt-get update':
		command => '/usr/bin/apt-get update';
	}

	file {
		'/home/vagrant/.bash_profile':
			owner => 'vagrant',
			group => 'vagrant',
			mode  => '0644',
			source => 'puppet:///modules/baseconfig/bash_profile';

		'/root/.bash_profile':
			owner => 'root',
			group => 'root',
			mode  => '0644',
			source => 'puppet:///modules/baseconfig/bash_profile_root';

		'/root/.my.cnf':
			owner => 'root',
			group => 'root',
			mode  => '0644',
			source => 'puppet:///modules/baseconfig/my.cnf';

		'/home/vagrant/.my.cnf':
			owner => 'vagrant',
			group => 'vagrant',
			mode  => '0644',
			source => 'puppet:///modules/baseconfig/my.cnf';
	}
}
