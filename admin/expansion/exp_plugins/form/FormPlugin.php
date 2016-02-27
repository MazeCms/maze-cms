<?php

namespace exp\exp_plugins\form;

use maze\base\Model;

class FormPlugin extends Model {


    public $id_plg;
    
    public $id_role;

    public $enabled;
    
    public $param;
        
    public function rules() {
        return [
            ['enabled','default', 'value'=>0],
            ['enabled', 'boolean'],            
            [['id_role', 'param', 'id_plg'], 'safe']
        ];
    }
      
    
    public function attributeLabels() {
        return[
            "id_role" => \Text::_("EXP_PLUGINS_FORM_LABEL_ACCESS"),
            "enabled"=>\Text::_("EXP_PLUGINS_FORM_LABEL_ENABLE")
        ];
    }

}
