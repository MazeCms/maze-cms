<?php

namespace ui\tmp;

use ui\select\Dropdown;
use  maze\table\InstallApp;
use maze\helpers\ArrayHelper;
use  maze\helpers\Html;

class TemplateImage extends Dropdown {

    public $condition = [];

    public function init() {
        parent::init();
        $this->options['options'] = [];
        $template = InstallApp::find()->where(['type'=>'template'])->all();
        
        foreach($template as $tmp)
        {
            $meta = \RC::getConf(["name" => $tmp->name, "type" => "template", "front" => $tmp->front_back]);
            $this->items[$tmp->name] = $tmp->name.' ('.$meta->get('name').')';
        
            $path = '@root'.($tmp->front_back == 1 ? '' : '/admin'). "/templates/" . $tmp->name . "/assets/preview.png";
            $this->options['options'][$tmp->name] = ['data-image'=> Html::pathThumb($path, 100, 60), 'data-description'=>$meta->get('description')];
        }
        
    }

}
