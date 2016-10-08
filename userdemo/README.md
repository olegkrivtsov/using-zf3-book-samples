User Demo Sample
==================================================

This sample is based on *Hello World* sample. It shows how to:

 * Create a new module named User
 * Create User entity
 * Implement user authentication (with login and password)
 * Implement access filter to restrict access to certain pages to authenticated users only
 * Implement user management UI
 * Init main menu items differently based on whether the current user is logged in or not

## Installation

You need to have PHP v.5.6 or later with `gd`, `intl` and `mcrypt` extensions.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework and Doctrine).

Enable development mode:

```
php composer.phar development-enable
```

Adjust permissions for `data` directory:

```
sudo chown -R www-data:www-data data
sudo chmod -R 775 data
```

Create `public/img/captcha` directory:

```
mkdir public/img/captcha
```

Adjust permissions for `public/img/captcha` directory:

```
sudo chown -R www-data:www-data public/img/captcha
sudo chmod -R 775 public/img/captcha 
```

Create `config/autoload/local.php` config file by copying its distrib version:

```
cp config/autoload/local.php.dist config/autoload/local.php
```

Edit `config/autoload/local.php` and set database password parameter.

Login to MySQL client:

```
mysql -u root -p
```

Create database:

```
CREATE DATABASE userdemo;
GRANT ALL PRIVILEGES ON userdemo.* TO userdemo@localhost identified by '<your_password>';
quit
```

Run database migrations to intialize database schema:

```
./vendor/bin/doctrine-module migrations:migrate
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/userdemo/public
    
	<Directory /path/to/userdemo/public/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the User Demo website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. 
Your feedback is highly appreciated.
