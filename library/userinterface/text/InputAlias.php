<?php

namespace ui\text;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use maze\helpers\StringHelper;
use RC;

class InputAlias extends Elements {
    
    public $options;
    
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
    }

    public function run() {
         $request = RC::app()->request;
         
        if($request->isAjax() && $request->get('clear') == 'ajax' && $request->get('fieldalias')){
            RC::app()->response->format = 'ajax';
            RC::app()->response->data =  ['alias'=>StringHelper::tarslateAlias($request->get('fieldalias'))];
        }
        $url = new \URI(\URI::instance());
        $url->setVar('clear', 'ajax');
        $textJs = "$.getJSON('".$url->toString()."', {fieldalias:$( '#".$this->options['id'] . "').val()},function(data){"
                . "$( '#".$this->options['id'] . "').val(data.alias)}); return false;";
        $tag = '<div class="input-group">';
        $tag .=  Html::textInput($this->name, $this->value, $this->options);
        $tag .= '<span class="input-group-btn"><button class="btn btn-default" onclick="'.$textJs.'" type="button"><span aria-hidden="true" class="glyphicon glyphicon-refresh"></span></button></span>';
        $tag .= '</div>';
        return $tag;
    }


}
