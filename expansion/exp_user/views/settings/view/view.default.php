<?php defined('_CHECK_') or die("Access denied");
class User_View_Settings extends View
{
	public function  registry()
	{
		$user = $this->_access->get();		
		$settings = $this->_access->getOptions()->getAllProp();
		
		$user["lastvisitDate"] = $this->_lang->getDate($this->_config->get("format_date"), $user["lastvisitDate"], "strftime", true);
		$user["registerDate"] = $this->_lang->getDate($this->_config->get("format_date"), $user["registerDate"], "strftime", true);
		if($user["timeactiv"] == "0000-00-00 00:00:00")
		{
			$user["timeactiv"] = "";
		}
		else
		{
			$user["timeactiv"] = $this->_lang->getDate($this->_config->get("format_date"), $user["timeactiv"], "strftime", true);
		}
		
		
		$this->set($user);
		if($settings)	$this->set($settings);
		$this->set("params", $this->getParamObj());
	}
}
?>