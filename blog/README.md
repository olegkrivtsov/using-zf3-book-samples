Blog Sample
==================================================

This sample is based on the Hello World sample and it shows how to:

  * Integrate your web site with Doctrine library
  * Initialize database schema
  * Use entity manager
  * Create entities and define relations between entities
  * Create repositories

## Installation

You need to have PHP v.5.6 or later.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework).

Adjust permissions for `data` directory:

```
sudo chown -R www-data:www-data data
sudo chown -R 775 data
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

Now you should be able to see the Blog website by wisiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. Your feedback is highly appreciated.
