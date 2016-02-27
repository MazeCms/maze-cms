<?php

namespace ui\checkbox;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;

class Toggle extends Elements {

    public $settings;
    public $attribute;
    public $name;
    public $value;
    public $model;
    public $default = 0;

    public function init() {
        if (!isset($this->settings['id'])) {
            $this->settings['id'] = $this->getId();
        }
        if(is_object($this->model))
        {
          $this->settings['id'] = Html::getInputId($this->model, $this->attribute); 
        }

        $this->settings["handler"] = isset($this->settings["handler"]) ? 'onchange="' . $this->options["handler"] . '"' : '';
        $this->settings["class"] = isset($this->settings["class"]) ? " " . $this->settings["class"] : '';
        $this->settings["class_label"] = isset($this->settings["class_label"]) ? 'class="' . $this->settings["class_label"] . '"' : '';       
        $this->settings["option"] = isset($this->settings["option"]) ? $this->settings["option"] : false;
        $this->settings["disabled"] = isset($this->settings["disabled"]) && $this->settings["disabled"] == true ? 'disabled="disabled"' : "";
        $this->settings["type"] = isset($this->settings["type"]) && !empty($this->settings["type"]) ? $this->settings["type"] : "radio";
    }

    public function run() {
        $teg = '<div id="' . $this->settings['id'] . '">';
        if (isset($this->settings["option"]) && is_array($this->settings["option"])) {
            $id_set = 0;
            $name = $this->name;
            if (is_object($this->model)) {
                $value = Html::getAttributeValue($this->model, $this->attribute);
                $name =  Html::getInputName($this->model, $this->attribute);
            } else {
                $value = $this->value;
            }
            if($value === null)
            {
                $value = $this->default;
            }
            foreach ($this->settings["option"]  as $val => $label) {
                if(is_array($label))
                {
                    $val = isset($label['value']) ? $label['value'] : 'array(0)';
                    $label = isset($label['label']) ? $label['label'] : 'array(0)';
                }
                $checked = ((string) $val === (string) $value) ? 'checked="checked"' : "";               
                $teg .= '<input value="' . $val . '"  ' . $checked . ' ' . $this->settings["handler"] . ' ' . $this->settings["disabled"] . ' type="' . $this->settings["type"] . '" id="' . $this->settings['id'] . '-' . $id_set . '" name="' . $name . '" />';
                $teg .= '<label ' . $this->settings["class_label"] . ' for="' . $this->settings['id'] . '-' . $id_set . '">' . \Text::_($label) . '</label>';
                $id_set++;
            }
        }
        $teg .= "</div>";
         \Document::instance()->setTextScritp('$( "#'.$this->settings['id'].'" ).buttonset();',['wrap'=>\Document::DOCREADY]);
        return $teg;
    }

}
