<?php

namespace ui\checkbox;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use ui\assets\AssetSwitchBtn;
use maze\base\JsExpression;
use maze\helpers\Json;
use maze\helpers\ArrayHelper;

class TrumbRadio extends Elements {

    public $options;
    public $attribute;
    public $settings = [];
    public $option = [];
    public $name;
    public $value;
    public $model;
    public $label;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (is_object($this->model)) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
        }
    }

    public function run() {

        if (is_object($this->model)) {
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
            $this->name = Html::getInputName($this->model, $this->attribute);
            if ($this->label == null) {
                $this->label = $this->model->getAttributeLabel(Html::getAttributeName($this->attribute));
            }
        }

        $this->settings = array_merge([
            'gridClass' => 'col-sm-3 col-md-3',
            'col' => 4,
            'width'=>200,
            'height'=>200,
            'options'=>[]
        ], $this->settings);

        $options['id'] = $this->options['id'];
        $options['class'] = 'maze-list-trumb-radio';
        $teg = Html::beginTag('div', $options);
        $col = 0;
        $count = 0;
        if(ArrayHelper::isAssociative($this->option)){
            $this->option = [$this->option];
        }
        
        foreach ($this->option as $val => $opt) {
            if ($col == 0) {
                $teg .= Html::beginTag('div', ['class' => 'row']);
            }
            if (isset($opt['img']) && isset($opt['label'])) {
                $teg .= Html::beginTag('div', ['class' =>  $this->settings['gridClass']]);
                $teg .= '<div class="thumbnail">';
                $teg .= Html::imgThumb($opt['img'], $this->settings['width'], $this->settings['height'], $this->settings['options']);
                $optionsInput = [];
                if ($this->value && $opt['value'] == $this->value) {
                    $optionsInput['checked'] = true;
                }
                $teg .= '<div class="caption radio">';   
                
                $teg .= "<label>".Html::input('radio', $this->name, $opt['value'], $optionsInput);
                $teg .= $opt['label']."</label>";
                $teg .= '</div>';
                $teg .= '</div>';
                $teg .= Html::endTag('div');
            }
            $col++;
            $count++;
            if($col >= $this->settings['col'] || $count >= count($this->option)){
                $col = 0;
                $teg .= Html::endTag('div');
            }
        }
        $teg .= Html::endTag('div');

        return $teg;
    }

}
