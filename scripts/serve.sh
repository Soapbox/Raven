#!/usr/bin/env bash

# Create a sites-available directory
mkdir /etc/httpd/sites-available 2>/dev/null
mkdir /etc/httpd/sites-enabled 2>/dev/null

# Create a logs directory
mkdir /var/logs/$1 2>/dev/null

# Create a ssl directory
mkdir /etc/ssl 2>/dev/null

# Generate our ssl keys
openssl genrsa -out "/etc/ssl/$1.key" 1024 2>/dev/null
openssl req -new -key /etc/ssl/$1.key -out /etc/ssl/$1.csr -subj "/CN=$1/O=Vagrant/C=UK" 2>/dev/null
openssl x509 -req -days 365 -in /etc/ssl/$1.csr -signkey /etc/ssl/$1.key -out /etc/ssl/$1.crt 2>/dev/null

block="<Directory \"$2\">
    AllowOverride All
</Directory>
<VirtualHost *:80>
    ServerName   \"$1\"
    DocumentRoot \"$2\"
</VirtualHost>"

echo "$block" > "/etc/httpd/sites-available/$1.conf"
ln -fs "/etc/httpd/sites-available/$1.conf" "/etc/httpd/sites-enabled/$1.conf"

service httpd reload
