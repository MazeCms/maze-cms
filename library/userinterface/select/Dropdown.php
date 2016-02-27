<?php

namespace ui\select;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetDropdown;

class Dropdown extends Elements {

    public $options;
    public $attribute;
    public $settings = [];
    public $items = [];
    public $value;
    public $name;
    public $model;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (is_object($this->model)) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            $name = $this->attribute;
            $this->value = $this->model->$name;
            $this->name = Html::getInputName($this->model, $this->attribute);
        }
    
        
    }

    public function run() {

        $html = Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        \RC::app()->document->setTextScritp('$( "#'.$this->options['id'] . '" ).msDropdown('.Json::encode($this->settings).');', 
                    ['wrap'=>\Document::DOCREADY]);
        return $html;
    }
    
    public function registerClientScript() {
        AssetDropdown::register();
    }

}
