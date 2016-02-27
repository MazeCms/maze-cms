<?php

namespace exp\exp_user\form;

use maze\helpers\ArrayHelper;
use maze\table\Users;
use maze\helpers\DataTime;
use Text;

class Editpass extends \maze\base\Model {

    public $password;
    
    public $repeatpassword;
    
    public $sendemail;
        
    public function rules() {
        return [
            [['password', 'repeatpassword'], "required"],
            ['password', 'match', 'pattern'=>'/^[a-z0-9-_.\#]{6,15}$/i', 
                'message'=>Text::_('EXP_USER_REC_PASS_NOMATCH')],
            ['repeatpassword', 'compare', 'compareAttribute'=>'password'],
            [['sendemail'], 'boolean']
        ];
    }
    
    
    public function attributeLabels() {
        return[
            "password" => Text::_("EXP_USER_PASS"),
            "repeatpassword" => Text::_("EXP_USER_PASS_VERIFIED"),
            "sendemail"=>Text::_("EXP_USER_REC_SENDPASS")
        ];
    }

}
