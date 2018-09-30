Hello World Sample
==================================================

This sample is based on *Zend Skeleton Application*. It shows how to:

 * Register controllers
 * Create controller actions
 * Use layouts and switch between them
 * Generate URLs with the `Url` controller plugin and the `Url` view helper
 * Create a custom route type
 * Create own view helpers 

## Installation

You need to have Apache 2.4 HTTP server, PHP v.5.6 or later plus `gd` and `intl` PHP extensions.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework).

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/helloworld/public
    
    <Directory /path/to/helloworld/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the Hello World website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. Your feedback is highly appreciated.
