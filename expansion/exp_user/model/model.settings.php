<?php defined('_CHECK_') or die("Access denied");
class User_Model_Settings extends Model
{
	
	public function save($data)
	{
		$acc_user =	$this->_access->get();
		$result = array();
		$table = $this->loadTable("user");
			
			
		if(isset($data["id_user"]) && $acc_user["id_user"] == $data["id_user"])
		{
			if(isset($data["name"]) && !empty($data["name"]))
			{
				if($this->hasValue($data, "avatar"))
				{
					$avatar = preg_replace("#^http[s]*:\/\/[a-z-_0-9.]+\.[a-z]{2,6}#","",$data["avatar"]);
				}
				else
				{
					$avatar = "";
				}
				$user = array(
					"id_user"			=>	$data["id_user"],
					"name"				=> 	$data["name"],
					"avatar"			=>	$avatar,
					"id_lang"			=>	$this->isValue($data, "id_lang")
				);
				
				$table->setData($user);
			
				if($table->save())
				{
					if(isset($data["meta"]))
					{
						$meta = array();
						
						foreach($data["meta"] as $key=>$val)
						{
							if(empty($val)) continue;
							$meta[] = array("id_user"=>$data["id_user"], "key_meta"=>$key, "value_meta"=>$val);
						}
						
						$this->_db->delete("PREF__user_meta", "id_user = '".$data["id_user"]."'");
						if(!empty($meta))
						{
							$this->_db->insertList("PREF__user_meta", $meta);
						}
						
						$result["ok"] = Text::_("EXP_USER_SETTINGS_OK_SAVE", array($acc_user["username"]));
						
					}
				}
				else
				{
					$result["err_save"] =  Text::_("EXP_USER_SETTINGS_ERROR_SAVE");
				}
				
				
			}
			else
			{
				$result["err_name"] = Text::_("EXP_USER_LOGIN_NAME_EMPTY");
			}
		}
		else
		{
			$result["err_user"] = Text::_("EXP_USER_NOT_USER");
		}
			
		return $result;
	}
	
}
?>