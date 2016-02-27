<?php 
class Captchaver_Plugin_Install extends Install
{
	public $front = 1;
	
	public $enabled = 1;
	
	public $group = "captcha";

	public $lang = array("ru-RU");					
	
	public $default_lang = "ru-RU";
	
	/***************************** установочные ступени ***********************************************/
	
	public $check = "PLG_CAPTCHA_CAPTCHAVER_CHECK";
	
	public $scripts_install = "PLG_CAPTCHA_CAPTCHAVER_INSTALL";
		
	public $remove_install = "PLG_CAPTCHA_CAPTCHAVER_REMOVEINSTALL";
	
}

?>