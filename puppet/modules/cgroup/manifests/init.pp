# == Class: cgroup
#
# Installs cgroup and enables it
#
class cgroup {
	package { ['libcgroup-dev']:
		ensure => present;
	}

	exec {
		'enable cgroup':
			cwd     => '/etc/default',
			path    => ['/bin', '/usr/bin'],
			command => 'sed -i \'s/GRUB_CMDLINE_LINUX_DEFAULT="/GRUB_CMDLINE_LINUX_DEFAULT="quiet cgroup_enable=memory swapaccount=1 /\' grub',
			unless  => 'grep cgroup_enable grub 2>/dev/null',
			require => Package['libcgroup-dev'],
			notify  => Exec['update-grub'];

		'enable cgroup cloudimg':
			cwd     => '/etc/default/grub.d',
			path    => ['/bin', '/usr/bin'],
			command => 'sed -i \'s/GRUB_CMDLINE_LINUX_DEFAULT="/GRUB_CMDLINE_LINUX_DEFAULT="quiet cgroup_enable=memory swapaccount=1 /\' *.cfg',
			unless  => 'grep cgroup_enable *.cfg 2>/dev/null',
			require => Package['libcgroup-dev'],
			notify  => Exec['update-grub'];

		'update-grub':
			path        => ['/bin', '/usr/bin', '/sbin', '/usr/sbin'],
			refreshonly => "true";
	}
}
