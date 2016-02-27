<?php defined('_CHECK_') or die("Access denied");
class User_Model_User extends Model
{
	public function getLoginMail($login)
	{
		$db = $this->_db;
		$query = "SELECT * FROM PREF__users WHERE username = '$login' OR email = '$login'";
		$result = $db->result_assoc($query);
		return $result;
	}
	
	/*
	////////////////////////////////////////////////////////
	// установка интервала через который можно восстановить пароль и кода активации временного пароля
	// @param $id_user (strig) - id текущего пользователя
	// @minutes $minutes (int) - время в минутах черех которое можно сново послать письмо с паролем восстановления
	///////////////////////////////////////////////////////
	*/
	
	public function setTimer($id_user, $minutes)
	{
		$timer = date('Y-m-d H:i:s', time() + 60*$minutes); 
		$activcod = RC::app()->session->generateKey(20);
		$user = array("timeactiv"=>$timer, "keyactiv"=>md5($activcod));		
		$where = "id_user = '".$id_user."'";		
		$this->_db->update("PREF__users",$user, $where);
		return md5($activcod); 
	}
	/*
	////////////////////////////////////////////////////////////////////
	// 					АКТИВАЦИЯ КОДА СМЕНЫ ПАРОЛЯ
	// @param $code (string) - искомый хер код пользователя
	////////////////////////////////////////////////////////////////////
	*/
	public function getActivCode($code)
	{
		$db = $this->_db;
		$query = "SELECT * FROM PREF__users WHERE keyactiv = '$code'";
		$result = $db->result_assoc($query);
		if(!empty($result))
		{
			$pass = RC::app()->session->generateKey(8);
			$user = array("keyactiv"=>null, "password"=>md5($pass));		
			$where = "keyactiv = '".$code."'";		
			$this->_db->update("PREF__users",$user, $where);
			$result["password"] = $pass;
			$result["keyactiv"] = null;
			return $result;
		}
		else return false;
	}
	
	public function getLang($lang)
	{
		$dbo = $this->_db;
		$query = "SELECT COUNT(*) FROM PREF__languages WHERE enabled = '1' AND lang_code = '$lang'";
		$result = $dbo->result($query);
		return ($result > 0 ) ? true : false;
	}
	
	public function getTimer($timeactiv)
	{
		$starttime = time();
		
		if( $starttime  > strtotime($timeactiv))
		{
			return true;
		}
		else
		{
			return false;
		}		 
	}
	
	public function getMinutes($timeactiv)
	{
		$start = time();
		$last = strtotime($timeactiv);		
		return round( ($last - $start)/60 );
	}
	
	public function isCaptcha()
	{
		
		if($this->_config->get("captcha") && $this->getParamObj()->getVar("log_captcha"))
		{
			return true;
		}
		return false;
	}
	
	public function isRCaptcha()
	{
		
		if($this->_config->get("captcha") && $this->getParamObj()->getVar("rec_captcha"))
		{
			return true;
		}
		return false;
	}
	
	public function loadAvatar($file)
	{
		$user = $this->_access->get();
		$param = $this->getParamObj();
		$path = $param->getVar("pathimage");
		$path = trim($path, "/");
		$path = PATH_ROOT.DS.implode(DS, explode("/", $path));
		$error = array();
		
		if(empty($user)) return false;
		
		if(!is_dir($path))
		{
			$error["nodir"] = true;
			return $error;
		}
		if(isset($file["tmp_name"]))
		{
			if($file["size"] > (int)$param->getVar("sizeimage")*1024*1024 )
			{
				$error["size"] = true;
				return $error;
			}
			if(!copy($file["tmp_name"], $path.DS.$file["name"]))
			{
				$error["copy"] = true;
				return $error;
			}
			if(!in_array(File::instance()->mime_content_type($path.DS.$file["name"]), $param->getVar("typeimage")))
			{
				$error["format"] = true;
				unlink($path.DS.$file["name"]);
				return $error;
			}
		}
		else
		{
			$error["nodir"] = true;
			return $error;
		}
		
		$resize = RC::factoryClass("Resize", array(
												array(	"small"		=>	$path,
																"width"		=>	$param->getVar("widthimage"),
																"height"	=>	$param->getVar("heightimage"),
																"ratio"		=>	true,
																"prefix"	=>	"avatar_")) );	
			
		$src = $resize->resize($path.DS.$file["name"], md5($file["name"].$user["name"].$user["email"]));
			
		@unlink($path.DS.$file["name"]);
		
		$error["src"] = $resize->getSrc($src);	
		
		return $error;
	}
	
	public function getAvatar($data)
	{
		$db = $this->_db;
		
		$db->SELECT("avatar");
		$db->FROM("PREF__users");
		if(isset($data["id"]))
		{
			$db->WHERE("id_user = '".$data["id"]."'");
		}
		elseif(isset($data["name"]))
		{
			$db->WHERE("username  = '".$data["name"]."'");
		}
		$avatar = $db->result_object($db->_query);
		
		if(empty($avatar)) Error::setError("EXP_USER_NOT_USER", 404);
		
		if(empty($avatar->avatar)) Error::setError("EXP_USER_NOT_USER", 404);
		
		$path = PATH_ROOT.DS.implode(DS,explode("/",trim($avatar->avatar, "/")));
		
		$options = array();
		
		$image = new Resize();
		
		$size = $image->getSize($path);
		
		if(!$size) Error::setError("EXP_USER_NOT_USER", 404);
		
		if(isset($data["h"]) && !empty($data["h"]))
		{
			$options["height"] = $data["h"];
		}
		else
		{
			$options["height"] =  $size["height"];
		}
		
		if(isset($data["w"]) && !empty($data["w"]))
		{
			$options["width"] = $data["w"];
		}
		else
		{
			$options["width"] =  $size["width"];
		}
		
		$options["ratio"] = isset($data["ratio"]) ? ($data["ratio"] ? true : false) : true; 
		
		$image->setOptions($options);
		
		$resize = $image->getResize($path);
		
		
		return array("content"=>$resize , "type"=>$image->getMimeType($path), "lenght"=>strlen($resize));	
		
	}
	
	
}
?>