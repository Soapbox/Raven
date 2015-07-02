if [ ! -d "/usr/share/Beansole" ]; then
	echo " ------------ Configuring Beanstalk... ------------ "
	echo "sudo bash -c 'beanstalkd -l 127.0.0.1 -p 11300 &' -u nobody" >> /etc/rc.local
	sudo bash -c 'beanstalkd -l 127.0.0.1 -p 11300 &' -u nobody

	# Install Beanstalkd Console
	cd /usr/share/
	git clone https://github.com/ptrofimov/beanstalk_console.git Beansole
	vhost="
	Alias /beansole /usr/share/Beansole/public
	Alias /beansolo /usr/share/Beansole/public
	Alias /beanstalk /usr/share/Beansole/public
	"
	echo "$vhost" | sudo tee /etc/httpd/conf.d/beansole.app.conf
	sudo chmod -R 777 /usr/share/Beansole/storage.json # Make storage folder writable by the web server
fi
