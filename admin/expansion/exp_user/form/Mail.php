<?php

namespace exp\exp_user\form;

use maze\base\Model;

class Mail extends Model {
    
    public $theme;
    
    public $mess;
            
    public function rules() {
        return [
            [["theme"], "required"],
            ['mess', 'safe']
        ];
    }
   
    public function attributeLabels() {
        return[
            "theme" => \Text::_("EXP_USER_MAILINFORMATION_LABEL_THEME"),
            "mess"=>"Текст сообщения"
        ];
    }

}
