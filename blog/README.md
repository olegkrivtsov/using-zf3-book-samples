Blog Sample
==================================================

This sample is based on the Hello World sample and it shows how to:

  * Integrate your web site with Doctrine library
  * Initialize database schema
  * Use entity manager
  * Create entities and define relations between entities
  * Create repositories

## Installation

You need to have PHP v.5.6 or later and MySQL v.5.6 or later.

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
CREATE DATABASE blog;
GRANT ALL PRIVILEGES ON blog.* TO blog@localhost identified by '<your_password>';
quit
```

Create tables and import data to database:

```
mysql -u root -p blog < data/schema.mysql.sql
```

Alternatively, you can run database migrations:

```
./vendor/bin/doctrine-module migrations:migrate
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/blog/public
    
	<Directory /path/to/blog/public/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```
After creating the virtual host, restart Apache.

Now you should be able to see the Blog website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. Your feedback is highly appreciated.
