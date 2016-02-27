<?php

namespace exp\exp_user\form;

use maze\helpers\ArrayHelper;
use maze\table\Users;
use maze\helpers\DataTime;

class Recover extends \maze\base\Model {

    public $login;
        
    public function rules() {
        return [
            ['login', "required"],
            ['login', 'validLogin']
        ];
    }
    
    public function validLogin($attribute, $params)
    {
        $user = Users::find()->where(['or', ['username'=>$this->login], ['email'=>$this->login]])->one();
        if(!$user)
        {
            $this->addError('login', \Text::_("EXP_USER_NOT_USER"));
        }
        else
        {		
            if($user->timeactiv && time() < strtotime($user->timeactiv))
            {
                $this->addError('login', \Text::_("EXP_USER_TIMER_NOT",['m'=>DataTime::diffMinutes(time(), $user->timeactiv)]));
            }
        }
       
    }
    public function attributeLabels() {
        return[
            "login" => \Text::_("EXP_USER_LOGIN")
        ];
    }

}
