# WPDIFF tool

This tool allows you to compare two installations of WordPress. Useful for malware detection, checking if original
Wordpress source is modified etc. 

## Features

* Able to download original WordPress version from wordpess.org automatically
* Uses php-diff (https://github.com/chrisboulton/php-diff) to generate html diffs

## Requirements

* PHP >= 5.3
* PHP should be compiled with: zip, curl

## Common usecases

### Compare some wordpress installation with it's original version:

```php
<?php
    $wp_local = new WPDIFF_Wordpress_Probationer('/path/to/my/wordpress/docroot');
    $wp_orig = new WPDIFF_Wordpress_Original($wp_local->getVersion());
    $differer = new WPDIFF_Differer($wp_local,$wp_orig);
    var_dump($differer->filesDiff());
?>    
```

### Disable comparison of themes and uploads directories:

```php
<?php
    $wp_local = new WPDIFF_Wordpress_Probationer('/path/to/my/wordpress/docroot');
    $wp_orig = new WPDIFF_Wordpress_Original($wp_local->getVersion());

    $wp_local->disableThemes();
    $wp_local->disableUploads();

    $wp_orig->disableThemes();
    $wp_orig->disableUploads();

    $differer = new WPDIFF_Differer($wp_local,$wp_orig);
    var_dump($differer->filesDiff());
?>    
```

### Compare two releases of wordpress:

```php
<?php
    $wp_orig1 = new WPDIFF_Wordpress_Original('3.4.1');
    $wp_orig2 = new WPDIFF_Wordpress_Original('3.4.2');
    $differer = new WPDIFF_Differer($wp_orig1,$wp_orig2);
    var_dump($differer->filesDiff());
?>    
```


