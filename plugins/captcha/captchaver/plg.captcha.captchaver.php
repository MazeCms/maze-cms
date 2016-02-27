<?php 
$root = dirname(__FILE__);
RC::import("$root|class|captcha", array("suffix"=>"php"));

class Captchaver_Plugin_Captcha extends Plugin
{
	public function captchaver($name, $namespace)
	{
		
		$allowed_symbols = "";
		
		if(is_array($this->params->getVar("symbols")))
		{
			if(in_array("number", $this->params->getVar("symbols")))
			{
				$allowed_symbols .= "0123456789";
			}
			if(in_array("letters", $this->params->getVar("symbols")))
			{
				$allowed_symbols .= "abcdegkmnpqsuvxyz";
			}
		}
		else
		{
			if($this->params->getVar("symbols") == "number")
			{
				$allowed_symbols .= "123456789";
			}
			elseif($this->params->getVar("symbols") == "letters")
			{
				$allowed_symbols .= "abcdegkmnpqsuvxyz";
			}
		}
		
		$captcha = new CAPTCHA(array(
			"length"							=>	$this->params->getVar("lengthcode"),
			"allowed_symbols"			=>	$allowed_symbols,
			"width"								=>	$this->params->getVar("width"),
			"height"							=>	$this->params->getVar("height"),
			"white_noise_density"	=>	$this->params->getVar("white_noise") ? 1/6 : false,
			"black_noise_density"	=>	$this->params->getVar("black_noise") ? 1/30 : false,
			"background_color"		=>	$this->params->getVar("background") ? array(mt_rand(220,255), mt_rand(220,255), mt_rand(220,255)) : array(255, 255, 255)
		));
		
		$captcha->Output();
		$this->_ses->set($name, $captcha->getKeyString(), $namespace);
	}
}

?>