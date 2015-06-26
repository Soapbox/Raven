#!/usr/bin/env bash

mkdir /var/logs/$1 2>/dev/null
mkdir /etc/ssl 2>/dev/null
openssl genrsa -out "/etc/ssl/$1.key" 1024 2>/dev/null
openssl req -new -key /etc/ssl/$1.key -out /etc/ssl/$1.csr -subj "/CN=$1/O=Vagrant/C=UK" 2>/dev/null
openssl x509 -req -days 365 -in /etc/ssl/$1.csr -signkey /etc/ssl/$1.key -out /etc/ssl/$1.crt 2>/dev/null

block="
    <VirtualHost $1:80>
        ServerName   "$1"
        DocumentRoot "$2"

        ErrorLog     "/var/logs/$1/error.log"
        CustomLog    "/var/logs/$1/access.log" combined
    </VirtualHost>
    <VirtualHost $1:443>
            SSLEngine on
            SSLCipherSuite ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP

            SSLCertificateFile      /etc/ssl/$1.crt
            SSLCertificateKeyFile   /etc/ssl/$1.key

            ServerName      "$1"
            DocumentRoot    "$2"

            CustomLog       "/var/logs/$1/access.log" combined
            ErrorLog        "/var/logs/$1/error.log"
    </VirtualHost>
"

echo "$block" > "/etc/apache2/sites-available/$1.conf"
ln -fs "/etc/apache2/sites-available/$1" "/etc/apache2/sites-enabled/$1"

sudo a2ensite $1.conf
