# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

@script = <<SCRIPT

# PHP
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y apache2 git curl php7.0 php7.0-bcmath php7.0-bz2 php7.0-cli php7.0-curl php7.0-intl php7.0-json php7.0-mbstring php7.0-opcache php7.0-soap php7.0-sqlite3 php7.0-xml php7.0-xsl php7.0-zip libapache2-mod-php7.0 php7.0-mysql

# XDEBUG
apt-get install php-xdebug
echo "\n[xdebug]" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.default_enable=1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_autostart=1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_connect_back=1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_enable=1" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_handler=dbgp" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_port='9000'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_host='127.0.0.1'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.idekey='PHPSTORM'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.remote_mode='req'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.var_display_max_depth='-1'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.var_display_max_children='-1'" >> /etc/php/7.0/mods-available/xdebug.ini
echo "xdebug.var_display_max_data='-1'" >> /etc/php/7.0/mods-available/xdebug.ini

# APACHE
echo "<VirtualHost *:80>
	DocumentRoot \"/var/www/public\"
	AllowEncodedSlashes On

	ServerName "using-zf3-book-samples.blog.local.vm";
	ServerAlias "www.using-zf3-book-samples.blog.local.vm";

	<Directory \"/var/www/public\">
		DirectoryIndex index.php
        AllowOverride All
        Require all granted
	</Directory>

</VirtualHost>" > /etc/apache2/sites-available/000-default.conf
a2enmod rewrite
service apache2 restart

# DATABASE
debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
apt-get install -y mysql-server
mysql -u root -proot -e "CREATE DATABASE blog;"
mysql -u root -proot -e "GRANT ALL PRIVILEGES ON blog.* TO blog@localhost identified by '<password>';"

# COMPOSER
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
cd /var/www
composer install
composer development-enable

#CONFIG
cp config/autoload/local.php.dist config/autoload/local.php

# DB MIGRATION
cd /var/www/
yes "y" | ./vendor/bin/doctrine-module migrations:migrate

# OTHER
sudo chown -R www-data:www-data ./data
sudo chmod -R 775 ./data

if ! grep "cd /var/www" /home/vagrant/.profile > /dev/null; then
    echo "cd /var/www" >> /home/vagrant/.profile
fi

echo "** [ZF] Run the following command to install dependencies, if you have not already:"
echo "    vagrant ssh -c 'composer install'"
echo "** [ZF] Visit http://localhost:8080 in your browser for to view the application **"
SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = 'ubuntu/trusty64'
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder '.', '/var/www'
  config.vm.provision 'shell', inline: @script

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
  end

end
