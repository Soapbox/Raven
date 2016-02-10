require 'json'
require 'yaml'

VAGRANTFILE_API_VERSION = "2"
confDir = $confDir ||= File.expand_path("~/.soapbox")

soapboxYamlPath = confDir + "/Soapbox.yaml"
afterScriptPath = confDir + "/after.sh"
aliasesPath = confDir + "/aliases"

require File.expand_path(File.dirname(__FILE__) + '/scripts/soapbox.rb')

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.network :forwarded_port, guest: 9200, host: 9200
    config.vm.network :forwarded_port, guest: 9300, host: 9300
    config.vm.network :forwarded_port, guest: 5601, host: 5601

	if File.exists? aliasesPath then
		config.vm.provision "file", source: aliasesPath, destination: "~/.bash_aliases"
	end

	Soapbox.configure(config, YAML::load(File.read(soapboxYamlPath)))

	if File.exists? afterScriptPath then
		config.vm.provision "shell", path: afterScriptPath
	end
end
