<?php defined('_CHECK_') or die("Access denied");
class SelectCaptcha extends Field
{
	protected $multi; // множественны список
	
	protected $placeholder; // предворительный текст в списке
	
	protected $handler; // обработчик события change

	protected $default_val;
	
	private static $_cache;
	
	private static $count = 1;
	
	public function __construct($name, $value, $options)
	{		
		$this->value = $value;
		$this->handler = isset($options["handler"]) ? $options["handler"] : false;
		$this->multi = isset($options["multi"]) ? $options["multi"] : false;
		$this->width = isset($options["width"]) ? $options["width"] : false;
		$this->default_val = isset($options["default"]) ? false : true;
		$this->_db = RC::getDBO();
		self::$count++;
		parent::__construct($name,'select');
						
	}
	
	public static function _($name, $value, $options = array("width"=>284))
	{
		return new self($name, $value, $options); 
	}
	
	public function get_html()
	{
		$teg = (string)SelectN::_($this->name, $this->value, $this->getEditor(),
		array("placeholder"	=>Text::_("LIB_USERINTERFACE_SELECT_CAPTCHA_PLACEHOLDER"), 
			  "handler"		=>$this->handler, 
			  "multi"		=> $this->multi, 
			  "width"		=> $this->width,
			  "id"			=> "captcha-".self::$count
		));
		return $teg;
		
	}
	
	protected function getEditor()
	{
		
		if(self::$_cache !== null) return self::$_cache;
		
		if($cache_f = $this->cache->get( array("SelectCaptcha","Field") )) return $cache_f;		
		$db = $this->_db;		
		
		$db->SELECT("p.name");
		$db->FROM("PREF__plugin AS p");
		$db->JOIN("PREF__install_app AS i");
		$db->ON("p.name = i.name");
		$db->JAND("i.type = 'plugin'");
		$db->JAND("i.front_back = '1'");
		$db->JAND("p.group_name = 'captcha'");
		$db->JAND("p.enabled = '1'");
		
		$editor = $this->_db->result_assoc_arr($db->_query);
		
		
		
		$option = array();
		if($this->default_val) $option[] = Text::_("LIB_USERINTERFACE_SELECT_TIMEZONE_DEFAULT");
		if(!empty($editor))
		{ 
			foreach($editor as $edit)
			{			
				$option[$edit["name"]] = $edit["name"];
			}
		}
		$option["none"] = Text::_("LIB_USERINTERFACE_SELECT_CAPTCHA_NONE");
		$this->cache->set(array("SelectCaptcha","Field") ,$option );
		 		 
		self::$_cache = $option;
		
		return $option;
	}
	
	
	
	public function check()
	{
		
	}
	
	
}

?>