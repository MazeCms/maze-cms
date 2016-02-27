<?php

namespace ui\editor;

use maze;
use RC;
use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetCodeMirror;

class Code extends Elements {

    public $options;
    public $settings = [];
    public $attribute;
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
        
        
        $this->options = array_merge([
            'rows' => 15,
            'cols' => 80,
            'class'=> 'form-control'
        ], $this->options);
        
         $this->settings = array_merge([
            'mode'=>'application/x-httpd-php',
            'lineNumbers' => true,
            'matchBrackets' => true,
            'indentUnit'=> 4,
            'indentWithTabs'=>true,
            'enterMode'=>'keep',
            'tabMode'=>'shift'
             
        ], $this->settings);
    }

    public function run() {

        \RC::app()->document->setTextScritp('var editCodeMirror = CodeMirror.fromTextArea(document.getElementById("'.$this->options['id'] . '"),'
                .Json::encode($this->settings).'); $("body").on("submit submiteditor",function(e){$("#'.$this->options['id'].'").val(editCodeMirror.getValue()); })',
            ['wrap'=>\Document::DOCREADY]);
        $html = Html::textarea($this->name, $this->value, $this->options);
        return $html;
    }
    
    public function registerClientScript() {
        AssetCodeMirror::register();
    }


}
