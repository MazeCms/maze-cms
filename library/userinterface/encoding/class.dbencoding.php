<?php defined('_CHECK_') or die("Access denied");
class DbEncoding extends Field
{
	protected $multi; // множественны список
	
	protected $placeholder; // предворительный текст в списке
	
	protected $handler; // обработчик события change
	
	private static $_cache;
	
	public function __construct($name, $value, $handler, $multi, $width)
	{		
		$this->value = $value;
		$this->handler = $handler;
		$this->multi = $multi;
		$this->width = $width;
		$this->_db = RC::getDBO();
		parent::__construct($name,'select');
						
	}
	
	public static function _($name, $value, $handler = false, $multi = false, $width = 284)
	{
		return new self($name, $value, $handler, $multi, $width); 
	}
	
	public function get_html()
	{
		$teg = (string)SelectN::_($this->name, $this->value, $this->getEncoding(),
			array("placeholder"	=>Text::_("LIB_USERINTERFACE_SELECT_ENCODING_PLACEHOLDER"),
			  "handler"		=>$this->handler,
			  "multi"		=> $this->multi, 
			  "width"		=> $this->width
			  ));
		return $teg;
		
	}
	
	protected function getEncoding()
	{
		if(self::$_cache !== null) return self::$_cache;
		
		$option = array();
		$result = $this->_db->result_arr_obj("SHOW charset");
		
		foreach($result as $val)
		{
			$option["$val->Charset"] = $val->Charset;
		}
				 
		self::$_cache = $option;
		
		return $option;
	}
	
	
	
	public function check()
	{
		
	}
	
	
}

?>