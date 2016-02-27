<?php defined('_CHECK_') or die("Access denied");
class Mail
{
	protected $_to; //адрес электронной почты получателя
	
	protected $_theme; // тема сообщения
	
	protected $_body; // текст сообщения 
	
	protected $_headers; // заголовки сообщения
	
	protected $_from; // адрес отправителя
	
	protected $_reply; // адрес отправления копии письма
	
	protected $_file = false; // вложения
	
	protected $_image = false; // изображения в письме
	
	protected $_type = false; // флаг если тип контента text(false) или html(true)
	
	protected $_bbc; // получателей слепой точной копии
	
	protected $_boundary; // разделитель
	
	protected $_eol; // перевод строки
	
	protected $_charset; // кодирока письма
	
	protected $_error = false; // флаг ошибок в формировании письма
	
	private static $_instance;	
	
	private function __clone() {}
		
	public function __construct()
	{
		$this->_boundary = "--".md5(uniqid(time()));
		$this->_eol = "\n";
		$this->_charset = "utf-8";
	}
		
	public static function instance()
	{
		if( self::$_instance == null)
		{
			self::$_instance = new self();
		} 
		return self::$_instance ;
	}
	
	
	/*
	///////////////////////////////////////////////////////////////////////////
	// 							ТЕМА ПИСЬМА
	///////////////////////////////////////////////////////////////////////////
	// param $theme (string) - тема письма 
	////////////////////////////////////////////////////////////////////////////
	*/
	
	public function setTheme($theme)
	{
		// утсановка темы письма
		if(empty($theme)){ $this->_error = true; return false;}
		$this->_theme =  $this->encoding_header(strip_tags(stripslashes($theme))) ;
		
	}
	
	/*
	////////////////////////////////////////////////////////////////////////////////
	//							 УСТАНОВКА ПОЛУЧАТЕЛЯ
	////////////////////////////////////////////////////////////////////////////////
	// param $mail (array) - массив имен - email получателя почты вида
	// $mail["name"] = "имя" $mail["email"] = "test@loc.loc" если имени нет берется только $mail["email"]
	////////////////////////////////////////////////////////////////////////////////
	*/
	
	public function setRecipient($mail)
	{
		// адрес или адреса получателя писем(а)
		if(!is_array($mail) && empty($mail['email'])){ $this->_error = true; return false;}
		
		$this->_to = $this->getFormatMail($mail);		
	}
	
	/*
	////////////////////////////////////////////////////////////////////////////////////////
	//						ДОБАВИТЬ ПОЛУЧАТЕЛЕЙ СЛЕПОЙ ТОЧНОЙ КОПИИ (bcc).
	////////////////////////////////////////////////////////////////////////////////////////
	// param $mail (array) - массив имен - email получателя почты вида
	// $mail[0]["name"] = "имя" $mail[0]["email"] = "test@loc.loc" и тд. и тп.
	///////////////////////////////////////////////////////////////////////////////////////
	*/
	
	public function setBcc($mail)
	{
		if(!isset($mail[0]) && (!is_array($mail[0]) && empty($mail[0]['email']))) { $this->_error = true; return false;}
		
		$this->_bbc = array();
		foreach($mail as $val)
		{
			if(!is_array($val) && empty($val['email'])) continue;
			
			$mailName = $this->getFormatMail($val);
			array_push($this->_bbc, $mailName);				
		}
		
		
	}
	
	/*
	/////////////////////////////////////////////////////////////////////////////////////////
	// 									УСТАНОВКА ОТПРАВИТЕЛЯ
	//////////////////////////////////////////////////////////////////////////////////////////
	// param $from (array) - массив имен - email отправителя почты вида 
	// $from["name"] = "имя" $from["email"] = "test@loc.loc" если имени нет берется только $from["email"]
	//////////////////////////////////////////////////////////////////////////////////////////
	*/
	
	public function setSender($from)
	{
		// от кого письмо		
		if(!is_array($from) || empty($from['email'])) { $this->_error = true; return false;}
		
		$this->_from = $this->getFormatMail($from);
			
	}
	
	/*
	////////////////////////////////////////////////////////////////
	// 					УСТАНОВКА КОПИИ ПИСЬМА
	////////////////////////////////////////////////////////////////
	//	param $reply (array) - массив имен - email отправителя почты вида 
	// $reply["name"] = "имя" $reply["email"] = "test@loc.loc" если имени нет берется только $reply["email"]
	////////////////////////////////////////////////////////////////
	*/
	
	public function setReply($reply)
	{
		// копия письма
		if(!is_array($reply) && empty($reply['email'])) { $this->_error = true; return false;}
		
		$this->_reply = $this->getFormatMail($reply);	
		
	}
	
	protected function getFormatMail($arr)
	{
		$result = empty($arr['name']) ? trim($arr['email']) :  $this->encoding_header(trim($arr['name'])) . " <" . trim($arr['email']) . ">";
		return $result;
	}
	public function setType($type = true)
	{
		$this->_type = $type;
	}
	
	protected function encoding_header($str, $data_charset = false, $send_charset = false)
	{
		if(!$data_charset && !$send_charset)
		{
			$send_charset = $this->_charset;
			$data_charset = $this->_charset;
		}
		if($data_charset != $send_charset) 
		{
			$str = iconv($data_charset, $send_charset, $str);
  	 	}
   		return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
	}
	
	protected function setHeaders($header)
	{
		if($key = array_search($header, $this->_headers))
		{
			unset($this->_headers[$key]);
		}
		array_push($this->_headers, $header);		
	}
	
	public function setBody($text)
	{		
		$this->_body = $text;
	}
	
	
	protected function getHeaders()
	{
		$this->_headers = array();			
		$this->setHeaders("MIME-Version: 1.0");
		$this->setHeaders( 'Date: ' . date('r', $_SERVER['REQUEST_TIME']) );
		$this->setHeaders( 'Message-ID: <' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' );
		$this->setHeaders("X-Mailer: PHP/".phpversion()." and MAZE CMS Author Nikolay Bugaev");
		if($this->_file)
		{
			$this->setHeaders("Content-Type: multipart/mixed; boundary=\"".$this->_boundary ."\"");
		}
		elseif($this->_image)
		{
			$this->setHeaders("Content-Type: multipart/related; boundary=\"".$this->_boundary ."\"");
		}
		else
		{
			if($this->_type)
			{
				$this->setHeaders("Content-Type: text/html; charset=\"".$this->_charset."\"");
			}
			else
			{
				$this->setHeaders("Content-Type: text/plain; charset=\"".$this->_charset."\"");
			}		
		}
		if(!empty($this->_from))
		{
			$this->setHeaders("From: ".$this->_from);
			$this->setHeaders("Return-Path: ".$this->_from);
		}
		
		if(!empty($this->_bbc))
		{
			foreach($this->_bbc as $mailName)
			{
				$this->setHeaders("Bcc: ".$mailName);
			}
		}
		if(!empty($this->_reply))	$this->setHeaders("Reply-To: ".$this->_reply);
		
	}
	
	public function addFile($files)
	{
		if($this->_image) return false;
		
		$multipatr[] = "--".$this->_boundary;
		$multipatr[] = "Content-Type: text/html; charset=\"".$this->_charset."\"";
		$multipatr[] = "Content-Transfer-Encoding: 8bit";
		$multipatr[] = "";
		$multipatr[] = $this->_body;
		if(is_array($files))
		{
			foreach($files as $file)
			{
				$part = $this->createFile($file);
				if(!$part) continue;
				$multipatr[] = $part;
			}
		}
		else
		{			
			if($part = $this->createFile($files)) $multipatr[] = $part;
		}
		$multipatr[] = "--".$this->_boundary."--";
		$this->_file = true;
		$this->_image = false;
		$this->_body = implode($this->_eol, $multipatr);
	}
	
	public function addStringFile($files)
	{
		if($this->_image) return false;
		
		$multipatr[] = "--".$this->_boundary;
		$multipatr[] = "Content-Type: text/html; charset=\"".$this->_charset."\"";
		$multipatr[] = "Content-Transfer-Encoding: 8bit";
		$multipatr[] = "";
		$multipatr[] = $this->_body;
		if(isset($files[0]))
		{
			foreach($files as $file)
			{
				$part = $this->createStringFile($file);
				if(!$part) continue;
				$multipatr[] = $part;
			}
		}
		else
		{			
			if($part = $this->createStringFile($files)) $multipatr[] = $part;
		}
		$multipatr[] = "--".$this->_boundary."--";
		$this->_file = true;
		$this->_image = false;
		$this->_body = implode($this->_eol, $multipatr);
	}
	
	protected function createFile($path)
	{
		 if(is_file($path))
		 {
			 $multipatrPart[] = "--".$this->_boundary;
             $fp =  fopen($path,"rb");
			 if(!$fp) return false;
			 $file = fread($fp,filesize($path));
             fclose($fp);            
             $multipatrPart[] = "Content-Type: application/octet-stream; name=\"".basename($path)."\"";			 			 	
			 $multipatrPart[] = "Content-Transfer-Encoding: base64";
			 $multipatrPart[] = "Content-Disposition: attachment; filename=\"".basename($path)."\"" ;
			 $multipatrPart[] = "";
			 $multipatrPart[] = chunk_split(base64_encode($file));	
           	return implode($this->_eol, $multipatrPart) ;            
		 }
		
		 return false;
	}
	protected function createStringFile($path)
	{
		 if(is_array($path) && isset($path["name"]) && isset($path["file"]))
		 {
			 $multipatrPart[] = "--".$this->_boundary;
            
             $multipatrPart[] = "Content-Type: application/octet-stream; name=\"".$path["name"]."\"";			 			 	
			 $multipatrPart[] = "Content-Transfer-Encoding: base64";
			 $multipatrPart[] = "Content-Disposition: attachment; filename=\"".$path["name"]."\"" ;
			 $multipatrPart[] = "";
			 $multipatrPart[] = chunk_split(base64_encode($path["file"]));	
           	return implode($this->_eol, $multipatrPart) ;            
		 }
		return false;
	}
	
	public function addImages($images)
	{
		if($this->_file) return false;
		
		$multipatr[] = "--".$this->_boundary;
		$multipatr[] = "Content-Type: text/html; charset=\"".$this->_charset."\"";
		$multipatr[] = "Content-Transfer-Encoding: 8bit";
		$multipatr[] = "";
		$multipatr[] = ($this->_eol == "\n") ? str_replace("/r/n", $this->_eol, $this->_body) : $this->_body;
		$multipatr[] = "";
		if(is_array($images))
		{
			foreach($images as $img)
			{
				$part = $this->createImg($img);
				if(!$part) continue;
				$multipatr[] = $part;
			}
		}
		else
		{			
			if($part = $this->createFile($images)) $multipatr[] = $part;
		}
		$multipatr[] = "";
		$multipatr[] = "--".$this->_boundary."--";
		$this->_image = true;
		$this->_file = false;
		$this->_body = implode($this->_eol, $multipatr);
	}
	
	protected function createImg($path)
	{
		 if(is_file($path))
		 {	
		 	// проверка является ли текущий файл изображением и удолетворяет разрешенным расширения		 
			$extentions = array(
			"#\.jpg#is",
			"#\.jpeg#is", 
			"#\.png#is", 
			"#\.gif#is",
			"#\.bpm#is",
			"#\.svg#is",
			"#\.ico#is",
			"#\.tga#is",
			"#\.tif#is"
			);
			$name  = basename($path);
			$ext = pathinfo($name, PATHINFO_EXTENSION);	// текущее расширение файла		
			$type = "";
			
			foreach($extentions AS $exten) 
			{
			  if(preg_match($exten, ".".$ext))
			  {
				   $type = $ext; // если совпадает то присваеваем тип
				   break;
			  }
			}
			
			if(empty($type)) return false;
			
			$multipatrPart[] = "--".$this->_boundary;
			$file = file_get_contents($path);  					   
			$multipatrPart[] = "Content-Type: image/".$type."; name=\"".$this->getImage($name)."\"";			 			 	
			$multipatrPart[] = "Content-Transfer-Encoding: base64";
			$multipatrPart[] = "Content-ID: <".$this->getImage($name).">";
			$multipatrPart[] = "";
			$multipatrPart[] = chunk_split(base64_encode($file), 76, $this->_eol);	
			return implode($this->_eol, $multipatrPart) ;            
			}
			return false;
	}
	// получить имя файла для вставки в контент
	public function getImage($name)
	{
		$pathName = pathinfo($name);
		return md5($pathName["filename"]).".".$pathName["extension"]; 
	}
	
	protected function clean()
	{
		$this->_bbc = false;
		$this->_body = false;
		$this->_to = false;
		$this->_headers = false;
		$this->_type = false;
		$this->_file = false;
		$this->_image = false;
		$this->_error = false;
		
	}
	
	public function send()
	{
		$this->getHeaders();
		
		if($this->_error || empty($this->_to))
		{
			$this->clean();
			return false;
		}		
				
		
		if(mail($this->_to, $this->_theme, $this->_body, implode("\r\n", $this->_headers)))
		{
			$this->clean();	
			return true;
		}
		else
		{
			$this->clean();	
			return false;
		}
	}
	
}

?>