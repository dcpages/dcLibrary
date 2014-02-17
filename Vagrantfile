Vagrant.configure("2") do |config|

  config.vm.box = "precise-server-cloudimg-amd64-vagrant"
  config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/precise/current/precise-server-cloudimg-amd64-vagrant-disk1.box"

  static_ip = ENV['STATIC_IP'] || "192.168.50.254"
  config.vm.network "private_network", ip: static_ip

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on", "--memory", "2048"]
  end

  config.omnibus.chef_version = "11.4.0"

  config.vm.provision "chef_solo" do |chef|
    chef.roles_path = "./cookbooks/roles"
    chef.cookbooks_path = "./cookbooks"
    chef.add_role "vagrant"
  end

  if File.exists?("../syn-vagrant.sh")
    config.vm.provision "shell", path: "../syn-vagrant.sh"
  else
    print "WARNING: ../syn-vagrant.sh not found: please read the wiki\n\n"
  end

  config.vm.synced_folder ".", "/vagrant", owner: "www-data", group: "www-data", mount_options: ["dmode=777","fmode=777"]

  if File.exists?("Vagrantfile.local")
    external = File.read "Vagrantfile.local"
    eval external
  end

end