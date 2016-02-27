<?php

namespace ui\text;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

class InputSpinner extends Elements {
    
    public $options;
    public $attribute;
    public $settings = [];
    public $value;
    public $name;
    public $model;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (is_object($this->model)) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
            $this->name = Html::getInputName($this->model, $this->attribute);
        }
        $this->settings = array_merge([
            'min'=>1,
            'max'=>1000,
            'step'=>1
        ],$this->settings);
    }

    public function run() {
         \RC::app()->document->setTextScritp('$( "#'.$this->options['id'] . '" ).spinner('.Json::encode($this->settings).');',
            ['wrap'=>\Document::DOCREADY]);
        return Html::textInput($this->name, $this->value, $this->options);
    }


}
