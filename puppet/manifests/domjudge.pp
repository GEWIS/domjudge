# create a new run stage to ensure certain modules are included first
stage { 'pre':
	before => Stage['main']
}

stage { 'domjudge':
	require => Stage['main']
}

# add the baseconfig module to the new 'pre' run stage
class { 'baseconfig':
	stage => 'pre'
}

class { 'domjudge':
	stage => 'domjudge'
}

# set defaults for file ownership/permissions
File {
	owner => 'root',
	group => 'root',
	mode  => '0644',
}

include baseconfig, avahi, nginx, mysql, php, domjudgepackages, cgroup, domjudge