<?php

namespace ui\images;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetAddImages;

class AddImage extends Elements {

    public $multi = false;
    public $attribute;
    public $format = ['jpg','png','jpeg','gif'];
    public $options;
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

        if ($this->multi) {
            $this->name .= '[]';
        }
        
        $settingsFile = [
            'multi'=>true,
            'onlyURL'=>false,
            'title'=>\Text::_("LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG"),
            'startLoad'=>new JsExpression('function(){$("#'. $this->options['id'].'").find("a").addClass("state-load");}'),
            'iframeLoad'=>new JsExpression('function(){$("#'. $this->options['id'].'").find(".state-load").removeClass("state-load");}')
        ];
        $handler = 'function handler(files, fm){
            $.each(files, function(i, file){
                if(file.url.search(/'.implode('|',$this->format).'$/gi) == -1){return true;}
                selfObj.addImages("createImages",file.url);    
            })                
        }';
        if(isset($this->settings['max_img'])){
            $this->settings['max_img'] = (int) $this->settings['max_img'];
        }
        $this->settings = array_merge([
            'text_btn' => 'Добавить',
            'name' => $this->name,
            'max_img' => 10,
            'add' => new JsExpression('function(inst, e){var selfObj = this; cms.loadFileManager('.$handler.', '. Json::encode($settingsFile).')}')
                ], $this->settings);
    }

    public function run() {
        echo Html::beginTag('ul', $this->options);
        if (!is_array($this->value)) {
            $this->value = [$this->value];
        }
        if (!empty($this->value)) {
            foreach ($this->value as $val) {
                echo Html::tag('li', '', ['data-src' => $val]);
            }
        }

        echo Html::endTag('ul');
        \RC::app()->document->setTextScritp('$( "#' . $this->options['id'] . '" ).addImages(' . Json::encode($this->settings) . ');', ['wrap' => \Document::DOCREADY]);
    }

    public function registerClientScript() {
        AssetAddImages::register();
    }

}
