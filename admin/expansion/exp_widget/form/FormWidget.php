<?php

namespace exp\exp_widget\form;

use maze\base\Model;

class FormWidget extends Model {


    public $id_wid;
    
    public $name;

    public $title;
    
    public $position;

    public $time_cache;

    public $enable_cache;

    public $time_active;
    
    public $time_inactive;
    
    public $enabled;
    
    public $title_show;
    
    public $id_tmp;
    
    public $id_lang;
    
    public $id_role;
    
    public $id_menu;
    
    public $id_exp;
    
    public $param;
    
    public $php_code;
    
    public $enable_php;
    
    public $url;
    
    public $sort;
    
    
        
    public function rules() {
        return [
            [['name', 'title', 'position', 'id_tmp'], "required"],
            ['title', 'string', 'max' => 255],
            [['enabled', 'title_show', 'id_tmp', 'id_lang', 'enable_php', 'enable_cache'],'default', 'value'=>0],
            [['enable_cache', 'enabled', 'enable_php'], 'boolean'],            
            [['id_tmp', 'id_lang', 'time_cache', 'id_wid'], 'number'],
            ['time_cache', 'number', 'min'=>1, 'max'=> 1000,'skipOnEmpty'=>false],
            [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'],
            [['param', 'id_role', 'id_menu', 'id_exp', 'sort', 'url', 'php_code'], 'safe']
        ];
    }
      
    
    public function attributeLabels() {
        return[
            "name" => \Text::_("EXP_WIDGET_WIDGETS_TABLE_HEAD_TYPE"),
            "title"=>\Text::_("EXP_WIDGET_FORM_LABEL_TITLE"),
            "position"=>\Text::_("EXP_WIDGET_FORM_LABEL_POSITION"),
            "time_cache"=>\Text::_("EXP_WIDGET_FORM_LABEL_CACHE_TINE"),
            "enable_cache"=>\Text::_("EXP_WIDGET_FORM_LABEL_CACHE_ENABLED"),
            "time_active"=>\Text::_("EXP_WIDGET_FORM_LABEL_ACTIVEDATE"),
            "time_inactive"=>\Text::_("EXP_WIDGET_FORM_LABEL_INACTIVEDATE"),
            "enabled"=> \Text::_("EXP_WIDGET_FORM_LABEL_ENABLED"),
            "title_show"=>\Text::_("EXP_WIDGET_FORM_LABEL_SHOWTITLE"),
            "id_tmp"=>\Text::_("EXP_WIDGET_FORM_LABEL_TMP"),
            "id_lang"=>\Text::_("EXP_WIDGET_FORM_LABEL_LANG"),
            "id_role"=>\Text::_("EXP_WIDGET_FORM_LABEL_ROLE"),
            "id_menu"=>\Text::_("EXP_WIDGET_FORM_LABEL_MENU"),
            "id_exp"=>\Text::_("EXP_WIDGET_FORM_LABEL_APP"),
            "enable_php"=>\Text::_("EXP_WIDGET_FORM_LABEL_ENABLE_PHPCODE")
        ];
    }

}
