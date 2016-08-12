Form Demo Sample
==================================================

This sample is based on the *Hello World* sample. It shows how to:

 * Create a form model
 * Use the form model in a controller
 * Render the form with special form view helpers
 * Use form security elements (CAPTCHA, CSRF)
 * Upload files with forms

## Installation

You need to have PHP v.5.6 or later plus GD and Mbstring PHP extensions.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework).

Adjust permissions for `public/img/captcha` directory:

```
sudo chown -R www-data:www-data public/img/captcha
sudo chown -R 775 public/img/captcha 
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/formdemo/public
    
	<Directory /path/to/formdemo/public/>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the Form Demo website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. Your feedback is highly appreciated.
