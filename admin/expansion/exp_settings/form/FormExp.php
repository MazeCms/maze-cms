<?php

namespace exp\exp_settings\form;

use maze\base\Model;
use maze\helpers\ArrayHelper;
use RC;

class FormExp extends Model {

    public $id_tmp;
    

    public $time_cache;
    

    public $enable_cache;
    

    public $param;
    
 
    public $enabled;
    
    
    
    public function rules() {
        return [
            [['time_cache'], "required"],
            [['enabled', 'enable_cache'], 'boolean'],
            ['time_cache', 'number', 'min'=>10],
            ['id_tmp', 'number'],
            ['param', 'safe']
        ];
    }
   
  
    public function attributeLabels() {
        return[
            "time_cache" => \Text::_("EXP_SETTINGS_EXPANSION_LABEL_TIMECACHE"),
            "id_tmp" => \Text::_("EXP_SETTINGS_EXPANSION_LABEL_TMP"),
            "enable_cache" => \Text::_("EXP_SETTINGS_EXPANSION_LABEL_ENABCACHE"),
            "enabled" => \Text::_("EXP_SETTINGS_EXPANSION_LABEL_ENABAPP")     
        ];
    }

}
        