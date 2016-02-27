<?php

namespace exp\exp_admin\form;

use maze\base\Model;

class FormSetiingsGadget extends Model {

    public $title;
    
    public $id_gad;
        
    public $param;
    
    public function rules() {
        return [
            ["title", "required"],
            ['title', 'string', 'max'=>255, 'skipOnEmpty'=>false],
            [['param', 'id_gad'], 'safe']

        ];
    }
   
 
    public function attributeLabels() {
        return[
            'id_gad'=>'ID',
            "title" => \Text::_("EXP_ADMIN_FORM_SETTINGS_TITLE")
        ];
    }

}
