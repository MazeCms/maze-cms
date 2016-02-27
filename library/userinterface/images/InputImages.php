<?php

namespace ui\images;

use Text;
use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetAddImages;

class InputImages extends Elements {

    public $attribute;
    public $format = ['jpg','png','jpeg','gif'];
    public $options = ['class'=>'form-control', 'placeholder'=>'...'];
    public $optionsGroup = ['class'=>'input-group'];
    public $btnText = '<span aria-hidden="true" class="glyphicon glyphicon-plus"></span>';
    public $btnOptions = ['class'=>'btn btn-primary'];
    public $btnTextRemove = '<span aria-hidden="true" class="glyphicon glyphicon-remove"></span>';
    public $btnRemoveOptions = ['class'=>'btn btn-danger'];
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

        
        $settingsFile = [
            'multi'=>true,
            'onlyURL'=>false,
            'title'=>Text::_("LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG"),
            'startLoad'=>new JsExpression('function(){$("#'. $this->options['id'].'").closest("div").find("button").addClass("state-load");}'),
            'iframeLoad'=>new JsExpression('function(){$("#'. $this->options['id'].'").closest("div").find("button").removeClass("state-load");}')
        ];
        $handler = 'function handler(files, fm){'.
            '$.each(files, function(i, file){'.
                'if(file.url.search(/'.implode('|',$this->format).'$/gi) == -1){return true;}'.
                '$(selfObj).closest("div").find("input[type=text]").val(file.url);})}';
        
  
        $this->btnOptions['onclick'] =  new JsExpression('var selfObj = this; cms.loadFileManager('.$handler.', '. Json::encode($settingsFile).'); return false;');
        $this->btnRemoveOptions['onclick'] =  new JsExpression('$(this).closest("div").find("input[type=text]").val(""); return false;');
    }

    public function run() {
        echo Html::beginTag('div', $this->optionsGroup);
        echo Html::textInput($this->name, $this->value, $this->options);
        echo Html::beginTag('span', ['class'=>'input-group-btn']);
        echo Html::button($this->btnText, $this->btnOptions);
        if($this->btnTextRemove){
            echo Html::button($this->btnTextRemove, $this->btnRemoveOptions);
        }        
        echo Html::endTag('span');
        echo Html::endTag('div');
    }



}
