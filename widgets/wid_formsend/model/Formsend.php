<?php

namespace wid\wid_formsend\model;

use maze\base\Model;
use Text;

class Formsend extends Model {
    
    public $idform;

    public $email;
    
    public $name;
    
    public $text;
    
    public $phone;

    public function rules() {
        return [
            [["name", "email", "phone"], "trim"],
            [["name", "email", "phone"], "required"], 
            ["phone", "match", "pattern"=>"/^\+\d{1}\(\d{3}\)\s*\d{3}-\d{2}-\d{2}$/"],
            ['email', 'email'],
            [['text', 'idform'], 'safe']

        ];
    }
   
   
    public function attributeLabels() {
        return[
            "email"=>Text::_("WID_FORMSEND_FORM_LABEL_EMAIL"),
            "name" => Text::_("WID_FORMSEND_FORM_LABEL_NAME"),
            "text" => Text::_("WID_FORMSEND_FORM_LABEL_TEXT"),
            "phone" => Text::_("WID_FORMSEND_FORM_LABEL_PHONE")
        ];
    }

}
