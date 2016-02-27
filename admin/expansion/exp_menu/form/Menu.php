<?php

namespace exp\exp_menu\form;

use maze\base\Model;
use Text;

class Menu extends Model {
    
    public $id_group;
    
    public $name;
    
    public $description;
    
    public $code;


    public function rules() {
        return [
            ["name", "required"],
            ["code", "required", "on"=>'create'],
            ['name', 'string', 'max'=>255, 'skipOnEmpty'=>false],
            ["code", 'match', 'pattern'=>'/^[a-z0-9_]{4,64}$/i'],
            ["code", "unique", "targetClass"=>"\maze\\table\MenuGroup", "targetAttribute"=>"code", "on"=>'create'],
            [['description', 'id_group'], 'safe']

        ];
    }
   
    public function attributeLabels() {
        return[
            "code"=>Text::_("EXP_MENU_GROUP_CODE"),
            "name" => Text::_("EXP_MENU_TABLE_NAME_TITLE"),
            "description" => Text::_("EXP_MENU_VIEW_ADD_TMP_DES")
        ];
    }

}
