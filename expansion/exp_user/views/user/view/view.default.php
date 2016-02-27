<?php

defined('_CHECK_') or die("Access denied");
use maze\helpers\DataTime;

class User_View_User extends View {

    public function registry() {
       
//        $user = $this->_access->get();
//        $this->set("isUser", $user ? true : false);
//        $this->_doc->set("title", Text::_("EXP_USER_AUTHORIZATION"));
//
//        if ($user) {
//            $user["lastvisitDate"] = DataTime::format($user["lastvisitDate"]);
//            $this->set($user);
//        }

        //$this->set("captcha", $model->isCaptcha() ? "/user/?run=getCaptcha&propcap=login" : false);
        //$this->set("params", $this->getParamObj());
    }

}

?>