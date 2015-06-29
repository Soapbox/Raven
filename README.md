# SoapBox Vagrant

A quick little command to help provision and manage our SoapBox vagrant
environment for us.

Inspired by Laravel Homestead.

### Adding the Virtual Box
1. Create an account on https://atlas.hashicorp.com/
2. Request access to soapbox/soapbox-vagrant
3. Do the following
  - Run `vagrant box add soapbox/soapbox-vagrant`
  - If it fails run `vagrant box add soapbox/soapbox-vagrant https://atlas.hashicorp.com/soapbox/boxes/soapbox-vagrant

### Installing Soapbox-Vagrant
1. Create a directory ('~/.bin', '~/bin', etc)
2. Add the above directory to your $PATH variable
3. symlink the `soapbox` file into the directory created above (step 1)
4. Run `install.sh`
5. Run `composer install`

For Example
```
mkdir -p ~/.applications/bin
export PATH=$PATH:~/.applications/bin
cd ~/.applications
git clone git@github.com:SoapBox/soapbox-vagrant.git soapbox-vagrant
ln -s ~/.applications/soapbox-vagrant/soapbox ~/.applications/bin/soapbox
cd ~/.applications/soapbox-vagrant
composer install
```
