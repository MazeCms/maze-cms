<?php

namespace ui\text;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use ui\assets\AssetInputmask;
use RC;

class TextInputMask extends Elements {
    
    public $options;
    public $attribute;
    public $value;
    public $name;
    public $model;
    public $mask;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (is_object($this->model)) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
            $this->name = Html::getInputName($this->model, $this->attribute);
        }        
    }

    public function run() {
        AssetInputmask::register();
        RC::app()->document->setTextScritp('$( "#'.$this->options['id'].'" ).mask("'.$this->mask.'");',['wrap'=>\Document::DOCREADY]);
        return Html::textInput($this->name, $this->value, $this->options);
    }


}
