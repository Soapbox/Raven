class Soapbox
  def Soapbox.configure(config, settings)
    # Set The VM Provider
    ENV['VAGRANT_DEFAULT_PROVIDER'] = settings["provider"] ||= "virtualbox"

    # Configure Local Variable To Access Scripts From Remote Location
    scriptDir = File.dirname(__FILE__)

    # Configure The Box
    config.vm.box = "soapbox/soapbox-vagrant"
    config.vm.hostname = settings["hostname"] ||= "vagrant"

    config.ssh.forward_agent = settings["foward_agent"] ||= true

    # Configure A Private Network IP
    config.vm.network :private_network, ip: settings["ip"] ||= "192.168.10.11"

    # Configure A Few VirtualBox Settings
    config.vm.provider "virtualbox" do |vb|
      vb.name = settings["name"] ||= "soapbox-vagrant"
      vb.customize ["modifyvm", :id, "--memory", settings["memory"] ||= "1024"]
      vb.customize ["modifyvm", :id, "--cpus", settings["cpus"] ||= "2"]
      vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
      vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
      vb.customize ["modifyvm", :id, "--ostype", "RedHat_64"]
    end

    # Configure A Few VMware Settings
    ["vmware_fusion", "vmware_workstation"].each do |vmware|
      config.vm.provider vmware do |v|
        v.vmx["displayName"] = "soapbox-vagrant"
        v.vmx["memsize"] = settings["memory"] ||= 1024
        v.vmx["numvcpus"] = settings["cpus"] ||= 2
        v.vmx["guestOS"] = "redhat-64"
      end
    end

    # Standardize Ports Naming Schema
    if (settings.has_key?("ports"))
      settings["ports"].each do |port|
        port["guest"] ||= port["to"]
        port["host"] ||= port["send"]
        port["protocol"] ||= "tcp"
      end
    else
      settings["ports"] = []
    end

    # Default Port Forwarding
    default_ports = {
      80   => 8000,
      443  => 44300,
      3306 => 33060,
      5432 => 54320,
      9200 => 9200,
      9300 => 9300,
      5601 => 5601
    }

    # Use Default Port Forwarding Unless Overridden
    default_ports.each do |guest, host|
      unless settings["ports"].any? { |mapping| mapping["guest"] == guest }
        config.vm.network "forwarded_port", guest: guest, host: host
      end
    end

    # Add Custom Ports From Configuration
    if settings.has_key?("ports")
      settings["ports"].each do |port|
        config.vm.network "forwarded_port", guest: port["guest"], host: port["host"], protocol: port["protocol"]
      end
    end

    # Configure The Public Key For SSH Access
    if settings.include? 'authorize'
      config.vm.provision "shell" do |s|
        s.inline = "echo $1 | grep -xq \"$1\" /home/vagrant/.ssh/authorized_keys || echo $1 | tee -a /home/vagrant/.ssh/authorized_keys"
        s.args = [File.read(File.expand_path(settings["authorize"]))]
      end
    end

    # Copy The SSH Private Keys To The Box
    if settings.include? 'keys'
      settings["keys"].each do |key|
        config.vm.provision "shell" do |s|
          s.privileged = false
          s.inline = "echo \"$1\" > /home/vagrant/.ssh/$2 && chmod 600 /home/vagrant/.ssh/$2"
          s.args = [File.read(File.expand_path(key)), key.split('/').last]
        end
      end
    end

    # Register All Of The Configured Shared Folders
    if settings.include? 'folders'
      settings["folders"].each do |folder|
        mount_opts = folder["type"] == "nfs" ? ['actimeo=1'] : []
        config.vm.synced_folder folder["map"], folder["to"], type: folder["type"] ||= nil, mount_options: mount_opts
      end
    end

    # Install All The Configured Nginx Sites
    settings["sites"].each do |site|
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/serve.sh"
        s.args = [
          site["map"],
          site["to"],
          site["port"] ||= "80",
          site["ssl"] ||= "443",
          site["ip"] ||= settings["ip"]
        ]
      end
    end

    # Configure All Of The Configured Databases
    if settings.has_key?("databases")
        settings["databases"].each do |db|
          config.vm.provision "shell" do |s|
            s.path = scriptDir + "/create-mysql.sh"
            s.args = [db]
          end
        end
    end

    # Update Composer On Every Provision
    config.vm.provision "shell" do |s|
      s.inline = "/usr/local/bin/composer self-update"
    end
  end
end
