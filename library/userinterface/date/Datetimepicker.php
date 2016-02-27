<?php

namespace ui\date;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

class Datetimepicker extends Elements {

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
            'dateFormat'=>'yy-mm-dd',
            'timeFormat'=>'HH:mm:ss',
            'stepHour'=>1,
            'stepMinute'=>5,
            'stepSecond'=>5
        ],$this->settings);
        
    }

    public function run() {

        $html = Html::textInput($this->name, $this->value, $this->options);
        \RC::app()->document->setTextScritp('$( "#' .
                $this->options['id'] . '" ).datetimepicker('.Json::encode($this->settings).');',['wrap'=>\Document::DOCREADY]);
        return $html;
    }
    
    public function registerClientScript() {
        $doc = \RC::app()->document;
 
//	$doc->addScript("/library/jquery/jqueryui/all/datepicker-lang/jquery.ui.datepicker-ru-RU.js");	
//	$doc->addScript("/library/jquery/jqueryui/all/timepicker-lang/jquery-ui-timepicker-ru-RU.js");        
//        $doc->addScript("/library/jquery/jqueryui/all/jquery-ui-timepicker-addon.js");
//        $doc->addStylesheet("/library/jquery/jqueryui/all/jquery-ui-timepicker-addon.css");
    }
    
    

}
