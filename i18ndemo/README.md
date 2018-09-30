i18n Demo Sample
==================================================

This sample is based on the *Form Demo* sample. It shows how to:

 * Localize your view templates
 * Localize view helpers
 * Localize forms
 * Localize validator messages
 * Select a language in the user interface 

## Installation

You need to have Apache 2.4 HTTP server, PHP v.5.6 or later plus `intl`, `gd` and `mbstring` PHP extensions.

Download the sample to some directory (it can be your home dir or `/var/www/html`) and run Composer as follows:

```
php composer.phar install
```

The command above will install the dependencies (Zend Framework).

Adjust permissions for `public/img/captcha` directory (use `www-data` user/group on Ubuntu, `httpd` on CentOS):

```
sudo chown -R www-data:www-data public/img/captcha
sudo chown -R 775 public/img/captcha 
```

Switch to development mode by typing:

```
php composer.phar development-enable
```

Then create an Apache virtual host. It should look like below:

```
<VirtualHost *:80>
    DocumentRoot /path/to/i18ndemo/public
    
    <Directory /path/to/i18ndemo/public/>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
```

Now you should be able to see the i18n Demo website by visiting the link "http://localhost/". 
 
## License

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses). 

## Contributing

If you found a mistake or a bug, please report it using the [Issues](https://github.com/olegkrivtsov/using-zf3-book-samples/issues) page. Your feedback is highly appreciated.
