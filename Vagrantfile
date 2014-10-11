# -*- mode: ruby -*-
# vi: set ft=ruby :

box      = 'ubuntu/trusty64'
hostname = 'domjudge-dev'
domain   = 'dev'
ip       = '10.20.30.2'
ram      = '2048'

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = box
  config.vm.host_name = hostname + '.' + domain
  config.vm.network :private_network, ip: ip

  config.vm.provider "virtualbox" do |vb|
    vb.customize [
      'modifyvm', :id,
      '--name', hostname,
      '--memory', ram
    ]
  end

  config.ssh.forward_agent = true

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = 'puppet/manifests'
    puppet.manifest_file = 'domjudge.pp'
    puppet.module_path = 'puppet/modules'
  end
end
