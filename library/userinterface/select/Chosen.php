<?php

namespace ui\select;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetChosen;

class Chosen extends Elements {
    
    public $option;
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
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
            $this->name = Html::getInputName($this->model, $this->attribute);
        }
        
        if(is_array($this->option))
        {
            foreach($this->option as $opt)
            {
                $this->items[$opt['value']] = \Text::_($opt['label']);
            }
        }
        
        $this->settings = array_merge([
            'no_results_text'=>'По вашему запросу ни чего не найдено',
            'placeholder_text'=>'....',
            'disable_search_threshold'=>10,
            'allow_single_deselect'=>false,
            'width'=> '100%'
        ],$this->settings);
        
    }

    public function run() {

        $html = Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        \RC::app()->document->setTextScritp('$( "#'.$this->options['id'] . '" ).chosen('.Json::encode($this->settings).');',
            ['wrap'=>\Document::DOCREADY]);
        return $html;
    }
    
    public function registerClientScript() {
        AssetChosen::register();
    }

}
