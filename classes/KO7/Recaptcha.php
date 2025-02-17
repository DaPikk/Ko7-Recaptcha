<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Simple wrapper module for Googles reCAPTCHA library
 *
 * @author     	Kristjan Tarjus <kristjan@tarjus.ee>
 * @copyright  	Copyright (c) 2021 Kristjan Tarjus
 * @license    	MIT
 * @package 	Koseven
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
	 * Version
	 * @var string
	 */
	protected $_version;

	/**
	 * Score for v3
	 * @var string 
	 */
	protected $_rscore;
        
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
			$config = Kohana::$config->load('recaptcha');
		}
		$this->_public_key = $config['public_key'];
		$this->_private_key = $config['private_key'];
		$this->_version = $config['version'];
                $this->_theme = $config['theme'];
                $this->_dsize = $config['dsize'];
                $this->_dlang = $config['dlang'];
		$this->_rscore = ((isset($config['rscore'])?$config['rscore']:"0.5"); //onlu needed for v3
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
		$version = $this->_version;  // Check version in config
		if ($version === 'v3') {
	            // reCAPTCHA v3: Generate script and hidden token field
	            return $this->get_html_v3();
	        } else {
	            // reCAPTCHA v2: Render the usual widget (for backward compatibility)
	            return $this->get_html_v2();
	        }
	}

	// Generate HTML for reCAPTCHA v2
    	public function get_html_v2()
    	{
		return '<script src="https://www.google.com/recaptcha/api.js?explicit&hl='.$this->_dlang.'" async defer></script>'
                        . '<div class="g-recaptcha w100" data-sitekey="'.$this->_public_key.'" data-theme="'.$this->_theme.'" data-size="'.$this->_dsize.'"></div>';
	}

	// Generate HTML for reCAPTCHA v3
    	public function get_html_v3()
    	{
		
		$controller_name = Request::instance()->controller();
		$action_name = Request::instance()->action();
	        $action = strtolower($controller_name.":".$action_name);  // Define a default action (e.g., 'homepage'). Can be customized as needed.
	
	        // Generate the reCAPTCHA v3 HTML (involves including the reCAPTCHA script)
	        $html = '<script src="https://www.google.com/recaptcha/api.js?hl='.$this->_dlang.'&render='.$this->_public_key.'"></script>';
	        $html .= '<script>
	                    grecaptcha.ready(function() {
	                        grecaptcha.execute("'.$this->_public_key. '", {action: "' . $action . '"}).then(function(token) {
	                            // Add the token to the form
	                            document.getElementById("recaptcha-token").value = token;
	                        });
	                    });
	                  </script>';
	        $html .= '<input type="hidden" name="recaptcha_token" id="recaptcha-token">'; // Hidden field for the token
	
	        return $html;
    	}

	/**
	 * Returns bool true if successful, bool false if not.
	 *
	 * @param   string  $gRecaptchaResponse
	 * @return  bool
	 */
	public function check($response)
	{
		$version = $this->_version;  // Check version in config

		if ($version === 'v3') {
	            // reCAPTCHA v3 logic
	            return $this->check_v3($response);
	        } else {
	            // reCAPTCHA v2 logic (default)
	            return $this->check_v2($response);
	        }
			return FALSE;
		}

	// Verify reCAPTCHA v2 (existing method)
    	public function check_v2($response)
    	{
		$resp = $this->_recaptcha->setExpectedHostname($_SERVER['HTTP_HOST'])
	                                  ->verify($response, Request::$client_ip);
		if ($resp->isSuccess()) {
		    return TRUE;
		} else {
		    $this->_error = $resp->getErrorCodes();
		}
		return FALSE;
    	}

    	// Verify reCAPTCHA v3
   	public function check_v3($response)
    	{
		 // Verify the response token with Google
	        $verification = $this->_recaptcha->setExpectedHostname($_SERVER['HTTP_HOST'])
					->verify($response, Request::$client_ip);
	
	        // Check if the verification was successful and the score is valid
		if($verification->isSuccess() && $verification->getScore() >= $this->_rscore){
			return TRUE;
		}
	        else {
		    $this->_error = __CLASS__." ".$this->_version." verification failed";
		}
		return FALSE;
    	}

}
