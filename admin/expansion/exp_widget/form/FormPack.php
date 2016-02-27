<?php

namespace exp\exp_widget\form;

use maze\base\Model;

class FormPack extends Model {


    public $id_lang;
    
    public $id_role;
    
    public $title_show;
  
    public $time_cache;
    
    public $enable_cache;
    
    public $time_active;
    
    public $time_inactive;
    
    public function rules() {
        return [
            [['enable_cache', 'title_show', 'id_lang'],'default', 'value'=>0],
            [['enable_cache', 'title_show'], 'boolean'],            
            [['id_lang', 'time_cache'], 'number'],
            ['time_cache', 'number', 'min'=>1, 'max'=> 1000,'skipOnEmpty'=>false],
            [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'],
            [['id_role'], 'safe']
        ];
    }
    
    
    public function attributeLabels() {
        return[
            "id_lang"=>\Text::_("EXP_WIDGET_FORM_LABEL_LANG"),
            "id_role"=>\Text::_("EXP_WIDGET_FORM_LABEL_ROLE"),
            "title_show"=>\Text::_("EXP_WIDGET_FORM_LABEL_SHOWTITLE"),
            "time_cache"=>\Text::_("EXP_WIDGET_FORM_LABEL_CACHE_TINE"),
            "enable_cache"=>\Text::_("EXP_WIDGET_FORM_LABEL_CACHE_ENABLED"),
            "time_active"=>\Text::_("EXP_WIDGET_FORM_LABEL_ACTIVEDATE"),
            "time_inactive"=>\Text::_("EXP_WIDGET_FORM_LABEL_INACTIVEDATE"),
        ];
    }

}
