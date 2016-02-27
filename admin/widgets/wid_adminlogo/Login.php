<?php

namespace wid\wid_adminlogo;

use maze\helpers\ArrayHelper;


class Login extends \maze\base\Model {

    public $login;
    
    public $password;
    
    public $remember;
    
    public function rules() {
        return [
            [["login", "password"], "required"],
            [['remember'], 'boolean']
        ];
    }
    public function attributeLabels() {
        return[
            "login" => \Text::_("WID_ADMINLOGO_NAME_LOGO"),
            "password"=>\Text::_("WID_ADMINLOGO_PASS_LOGO"),
            "remember" => \Text::_("WID_ADMINLOGO_REMEMBER_LOGO")
        ];
    }
   

}
