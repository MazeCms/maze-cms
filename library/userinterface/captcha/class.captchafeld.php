<?php defined('_CHECK_') or die("Access denied");
class CaptchaFeld extends Field
{
	protected $_config;
	
	protected $namespace;
		
	public function __construct($name, $namespace)
	{		
		$this->namespace 	= $namespace;
		$this->_config 		= RC::getConfig();
		parent::__construct($name,'captcha');
						
	}
	
	public static function _($name, $namespace)
	{
		return new self($name, $namespace); 
	}
	
	public function get_html()
	{		
		if($this->_config->get("captcha"))
		{
			RC::getPlugin("captcha")->triggerHandler($this->_config->get("captcha"), array($this->name, $this->namespace) );
		}
	}
	
	public function check(){}
	
}

?>