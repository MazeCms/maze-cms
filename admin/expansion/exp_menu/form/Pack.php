<?php

namespace exp\exp_menu\form;

use maze\base\Model;

class Pack extends Model {

    public $meta_robots;
    public $time_active;
    public $time_inactive;
    public $id_tmp;
    public $id_lang;
    public $id_role;
    public $id_menu;

    public function rules() {
        return [
           [['id_lang', 'id_tmp'], 'number'],
           [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'], 
           [['meta_robots', 'id_role'], 'safe']
        ];
    }

    public function attributeLabels() {
        return[
            "id_lang" => \Text::_("EXP_MENU_ADD_ITEM_FORM_LANG"),
            "id_tmp" => \Text::_("EXP_MENU_ADD_ITEM_FORM_TMP"),
            "meta_robots" => \Text::_("EXP_MENU_ITEMS_PARAMS_META_LABEL_ROBOTS"),
            "time_active" => \Text::_("EXP_MENU_ADD_ITEM_FORM_TIMEACTIVE"),
            "time_inactive" => \Text::_("EXP_MENU_ADD_ITEM_FORM_TIMEINACTIVE"),
            "id_role" => \Text::_("EXP_MENU_ADD_ITEM_FORM_ACCESS"),
        ];
    }

}
