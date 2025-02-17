![PHP workflow](https://github.com/DaPikk/Ko7-Recaptcha/actions/workflows/php.yml/badge.svg)
![Required minimum PHP Version](https://img.shields.io/badge/PHP-=>7.4-blue)
![Required minimum Ko7even Version](https://img.shields.io/badge/Ko7even-=>3.3.8-blue)


# Simple wrapper for Google's reCAPTCHA library

A single class that allows you to add Google reCAPTCHA to your Ko7 forms. Code is very simple.

## Installation
Direct download:
Download the [ZIP file](https://github.com/DaPikk/Ko7-Recaptcha/archive/refs/heads/main.zip)
and extract into your project.

## Requires
* PHP => 7.4
* Ko7even => 3.3.8

## Usage
1. First obtain the appropriate keys for the type of reCAPTCHA you wish to
integrate for v2 at https://www.google.com/recaptcha/admin
2. Copy config from MODPATH/recaptcha/config/recaptcha.php to APPATH/config/recaptcha.php
3. Make needed changes to configuration array!
4. Activate recaptcha module in bootstrap!

To initialize reCaptcha check form on Your page:
```php
<?php echo Recaptcha::instance()->get_html(); ?>
```

To check verification from POST:
```php
<?php 
    $x = Recaptcha::instance()->check($_POST['g-recaptcha-response']);
    if($x == FALSE){
        echo __('Please resubmit Google reCaptcha check!');
    }else{
        echo __('Recaptcha check succeeded!!!!');
    } 
?>
```

## Config

recaptcha.php

```php
return array(
	'public_key'  => 'YOUR GOOGLE RECAPTCHA SITE KEY',
	'private_key' => 'YOUR GOOGLE RECAPTCHA SECRET KEY',
        'theme' => 'light',
	'dlang' => 'en',
        'dsize' => 'normal',
);

```


