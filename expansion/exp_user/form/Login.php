<?php

namespace exp\exp_user\form;

use maze\helpers\ArrayHelper;
use maze\table\Users;


class Login extends \maze\base\Model {

    public $login;
    
    public $password;
    
    public $remember;
    
    public function rules() {
        return [
            [["login", "password"], "required"],
            [["login"], 'validLogin'],
            [['remember'], 'boolean']
        ];
    }
    
    public function validLogin($attribute, $params)
    {
        if(Users::find()->where(['username'=>$this->login])->exists())
        {
            $user = Users::find()->where(['username'=>$this->login])->one();
            if($user->password !== md5($this->password))
            {                
                $this->addError('login', \Text::_("EXP_USER_LOGIN_NOT_PASS"));
            }
        }
        else
        {
            $this->addError('login', \Text::_("EXP_USER_LOGIN_NOT_LOGO"));
        }
    }
    public function attributeLabels() {
        return[
            "login" => \Text::_("EXP_USER_LOGIN"),
            "password" => \Text::_("EXP_USER_PASS")
        ];
    }

}
