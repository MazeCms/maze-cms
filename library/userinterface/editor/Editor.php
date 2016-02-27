<?php

namespace ui\editor;

use maze;
use RC;
use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;

class Editor extends Elements {

    public $options;
    public $editor;
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
        if (!$this->editor) {
            $config = RC::getConfig();
            $this->editor = defined("ADMIN") ? $config->editor_admin : $config->editor_site;
        } 
        
        $this->options = array_merge([
            'rows' => 15,
            'cols' => 80,
            'class'=> 'form-control'
        ], $this->options);
    }

    public function run() {

        RC::getPlugin("editor")->triggerHandler($this->editor, [$this->options['id'], $this]);
        $html = Html::textarea($this->name, $this->value, $this->options);
        return $html;
    }

}
