<?php

namespace ui\checkbox;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use ui\assets\AssetSwitchBtn;
use maze\base\JsExpression;
use maze\helpers\Json;

class SwitchBtn extends Elements {

    public $options;
    public $attribute;
    public $settings = [];
    public $name;
    public $value;
    public $model;
    public $label;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
       
        if(!isset($this->options['value']))
        {
            $this->options['value'] = 1;
        }
        if(is_object($this->model))
        {
          $this->options['id'] = Html::getInputId($this->model, $this->attribute); 
        }
    }

    public function run() {

            if (is_object($this->model)) {
                $this->value = Html::getAttributeValue($this->model, $this->attribute);
                $this->name =  Html::getInputName($this->model, $this->attribute);
                if($this->label == null)
                {
                   $this->label = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute)); 
                }
            } 
            $this->settings = array_merge(['draggable'=>false],$this->settings);
            
           
            $options['checked'] = $this->options['value'] == $this->value;
            $options['id'] = $this->options['id'];
            
           $teg = Html::tag('div',Html::hiddenInput($this->name, 0).' '.Html::input('checkbox', $this->name, $this->options['value'], $options).' '.Html::tag('span',$this->label), ['class'=>'maze-switch-bloc']);

            \Document::instance()->setTextScritp('$( "#'.$this->options['id'].'" ).mazeSwitch('.Json::encode($this->settings).');',['wrap'=>\Document::DOCREADY]);
        return $teg;
    }
    
    public function registerClientScript() {
        AssetSwitchBtn::register();
    }

}
