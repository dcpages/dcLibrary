Vagrant.configure('2') do |config|

  environment = ENV['APP_ENV'] || 'development'

  # Install Chef 11.4.4 on the VM
  config.omnibus.chef_version = '11.4.4'

  # Sync the project directory to /vagrant on the VM
  config.vm.synced_folder '.', '/vagrant', owner: 'www-data', group: 'www-data', mount_options: ['dmode=777','fmode=777']

  if environment == 'qa'
    # QA environment settings

    # Give the base box a project-specific name, but the same box url. This will
    # allow us to easily create a prebuilt base box on the QA server, decreasing
    # the setup time for Chef recipes.
    config.vm.box     = '%QA_APP_ENV%'
    config.vm.box_url = 'http://cloud-images.ubuntu.com/vagrant/precise/current/precise-server-cloudimg-amd64-vagrant-disk1.box'

    # QA environment uses DHCP on the host-only network adapter
    config.vm.network 'private_network', type: :dhcp

    # Need this to enable DNS host resolution through NAT so our VM can access
    # the internet
    config.vm.provider 'virtualbox' do |vb|
      vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on', '--memory', '1024']
    end

    config.vm.provision 'chef_solo' do |chef|
      chef.roles_path     = './cookbooks/roles'
      chef.cookbooks_path = './cookbooks'
      chef.add_role 'qa'

      app_name = ENV['APP_NAME'] || '%QA_APP_ENV%'

      chef.json = {
        'server' => {
          'server_name'    => app_name + '.%QA_HOST%',
          'server_aliases' => [ app_name + '.%QA_HOST%' ]
        },
        'lively' => {
          'server_name'    => 'lively.' + app_name + '.%QA_HOST%',
          'server_aliases' => [ 'lively.' + app_name + '.%QA_HOST%' ]
        },
        'etc_environment' => {
          'APP_NAME' => app_name
        }
      }
    end
  else
    config.vm.box = 'precise-server-cloudimg-amd64-vagrant'
    config.vm.box_url = 'http://cloud-images.ubuntu.com/vagrant/precise/current/precise-server-cloudimg-amd64-vagrant-disk1.box'

    static_ip = ENV['STATIC_IP'] || '192.168.50.%DEV_IP_BLOCK%'
    config.vm.network 'private_network', ip: static_ip

    config.vm.provider 'virtualbox' do |vb|
      vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on', '--memory', '2048']
    end

    config.vm.provision 'chef_solo' do |chef|
      chef.roles_path     = './cookbooks/roles'
      chef.cookbooks_path = './cookbooks'
      chef.add_role 'development'
    end
  end

  if File.exists?('../syn-vagrant.sh')
    config.vm.provision 'shell', path: '../syn-vagrant.sh'
  end

  if File.exists?('Vagrantfile.local')
    external = File.read 'Vagrantfile.local'
    eval external
  end
end
