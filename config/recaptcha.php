<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'public_key'  => 'YOUR GOOGLE RECAPTCHA SITE KEY',
	'private_key' => 'YOUR GOOGLE RECAPTCHA SECRET KEY',
	'version' => 'v2',
	'rscore' => NULL, //Minimum score for safe actions, defaults to 0.5 and up
        'theme' => 'light',
	'dlang' => 'en',
        'dsize' => 'normal',
);
