<?php 
class Shortcode_Plugin_Install extends Install
{
	public $front = 1;
	
	public $enabled = 1;
	
	public $group = "content";

	public $lang = array("ru-RU");					
	
	public $default_lang = "ru-RU";
	
	/***************************** установочные ступени ***********************************************/
	
	public $check = "PLG_CONTENT_SHORTCODE_CHECK";
	
	public $scripts_install = "PLG_CONTENT_SHORTCODE_INSTALL";
		
	public $remove_install = "PLG_CONTENT_SHORTCODE_REMOVEINSTALL";
	
}

?>