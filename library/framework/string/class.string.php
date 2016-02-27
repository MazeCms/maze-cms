<?php defined('_CHECK_') or die("Access denied");

class String
{
	private static $_instances;
	
	protected $charset;

	public function __construct()
	{
		$config	= RC::getConfig();
		$this->charset = $config->get("charset");	
	}

	public static function instance()
	{
		if (self::$_instances == null)
		{
			self::$_instances = new self();
		}
		return self::$_instances;
	}
	// обрезка строки
	public function subSrt($text, $start, $length)
	{
		return iconv_substr($text,$start, $length, $this->charset);
	}
	// длина строки
	public function strLen($text)
	{
		return iconv_strlen($text, $this->charset);
	}

	// проверка строки на json
	public static function isJson($string)
	{		
        try
        {            
            json_decode($string);
        }
        catch(ErrorException $e)
        {
            
            return false;
        }
 
        return true;
	}
	
	


	
}
