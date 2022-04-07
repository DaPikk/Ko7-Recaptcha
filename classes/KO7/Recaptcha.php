<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Simple wrapper module for Googles reCAPTCHA library
 *
 * @author     Kristjan Tarjus <kristjan@tarjus.ee>
 * @copyright  Copyright (c) 2021 Kristjan Tarjus
 * @license    MIT
 * @package Koseven
 */
class KO7_Recaptcha {

	/**
	 * Site key
	 * @var string
	 */
	protected $_public_key;

	/**
	 * Secret key
	 * @var string
	 */
	protected $_private_key;
        
        /**
	 * Theme to use
	 * @var string (dark|light)
	 */
	protected $_theme;
        
        /**
	 * Widget size
	 * @var string (compact|normal)
	 */
	protected $_dsize;
        
        /**
	 * Widget language
	 * @var string (en|...)
         * @see https://developers.google.com/recaptcha/docs/language
	 */
	protected $_dlang;
        
        /**
         * Recaptcha instance
         * @var bool 
         */
        protected $_recaptcha=FALSE;

	/**
	 * Error code returned when checking the answer
	 * @var string
	 */
	protected $_error;

	/**
	 * Load the reCAPTCHA PHP library and configure the keys from the config
	 * file or the provided array argument.
	 *
	 * @param   array  $config
	 * @return  object
	 */
	public function __construct(array $config = NULL)
	{
		require_once KO7::find_file('vendor', 'recaptcha/src/autoload');

		if (empty($config))
		{
			$config = KO7::$config->load('recaptcha');
		}
		$this->_public_key = $config['public_key'];
		$this->_private_key = $config['private_key'];
                $this->_theme = $config['theme'];
                $this->_dsize = $config['dsize'];
                $this->_dlang = $config['dlang'];
                if(!isset($this->_recaptcha)||empty($this->_recaptcha)||$this->_recaptcha===FALSE){
                    $this->_recaptcha = new \ReCaptcha\ReCaptcha($this->_private_key);
                }
	}
        
        public static function instance(){
                return new ReCaptcha;
        }

	/**
	 * Generate the HTML to display to the client
	 *
	 * @return  string
	 */
	public function get_html()
	{
		return '<script src="https://www.google.com/recaptcha/api.js?explicit&hl='.$this->_dlang.'" async defer></script>'
                        . '<div class="g-recaptcha w100" data-sitekey="'.$this->_public_key.'" data-theme="'.$this->_theme.'" data-size="'.$this->_dsize.'"></div>';
	}

	/**
	 * Returns bool true if successful, bool false if not.
	 *
	 * @param   string  $gRecaptchaResponse
	 * @return  bool
	 */
	public function check($gRecaptchaResponse)
	{
                $resp = $this->_recaptcha->setExpectedHostname($_SERVER['HTTP_HOST'])
                                  ->verify($gRecaptchaResponse, Request::$client_ip);
                if ($resp->isSuccess()) {
                    return TRUE;
                } else {
                    $this->_error = $resp->getErrorCodes();
                }
		return FALSE;
	}

}
