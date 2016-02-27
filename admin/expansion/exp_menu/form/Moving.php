<?php

namespace exp\exp_menu\form;

use maze\base\Model;

class Moving extends Model {

    /**
     * @var int - id меню 
     */
    public $id_group;
    
    public $parent;
    
    public $id_menu;
    
    public function rules() {
        return [
            [['id_group'], "required"],
            ['parent', 'default', 'value'=>0],
            [['id_group', 'parent'], 'number']
        ];
    }
    
    
    public function attributeLabels() {
        return[
            "id_group"=>\Text::_("EXP_MENU_TITLE_MENU"),
            "parent"=>\Text::_("EXP_MENU_ADD_ITEM_FORM_MENU"),
        ];
    }

}
