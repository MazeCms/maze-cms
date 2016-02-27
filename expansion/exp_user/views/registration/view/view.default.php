<?php defined('_CHECK_') or die("Access denied");
class User_View_Registration extends View
{
	public function  registry()
	{
		$model = $this->loadModel("Registration");
		$this->_doc->set("title", Text::_("EXP_USER_PASS_REG"));
		$this->set("captcha", $model->isCaptcha() ? "/user/?run=getCaptcha&propcap=registration" : false);
		$this->set("params", $this->getParamObj());
	}
}
?>