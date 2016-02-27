<?php

namespace ui\exp;

use ui\select\Chosen;
use  maze\table\InstallApp;
use maze\helpers\ArrayHelper;

class ExpList extends Chosen {

    public $condition = [];

    public function init() {
        parent::init();
        $this->items = [];
        $exp = InstallApp::find()->where(array_merge($this->condition, ['type'=>'expansion']))->innerJoinWith('expansion')->all();
        foreach($exp as $e)
        {
            $info = \RC::getConf(array("type"=>"expansion", "name"=>$e->name));
            $this->items[$e->expansion->id_exp] = $info->get("name") ? $info->get("name") : $e->name;
        }
        
    }

}
