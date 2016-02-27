<?php

namespace exp\exp_contents\ui;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use Text;
use RC;
use exp\exp_contents\model\ModelContent;
use exp\exp_contents\form\FilterContent;

class InputContent extends Elements {
    
    public $option;
    public $options;
    public $attribute;
    public $items = [];
    public $value;
    public $label;
    public $name;
    public $model;
    
    protected static $filteFlag = false;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (is_object($this->model)) {
            $this->options['id'] = Html::getInputId($this->model, $this->attribute);
            $this->value = Html::getAttributeValue($this->model, $this->attribute);
            $this->name = Html::getInputName($this->model, $this->attribute);
        }
        if(!isset($this->options['class'])){
            $this->options['class'] = 'form-control';
        }

        $this->options['disabled'] = "disabled";
        if($this->value){
            $model = new ModelContent;
            if($model->find($this->value)){
                $this->label = $model->getTitle();
            }else{
                $this->value = null;
            }
        }
        
        
    }

    public function run() {
        $html = '<div class="input-group">';
        $html .= Html::textInput($this->name, $this->label, $this->options);
        $html .= Html::hiddenInput($this->name, $this->value);
        $html .= '<span class="input-group-btn"><button class="btn btn-primary" type="button" onclick="cms.inputContent(this)" ><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> Выбрать</button></span>';
        $html .='</div>';
        if(!static::$filteFlag){
            $modelFilter = new FilterContent;
           $html .= RC::app()->getView()->render('@exp/exp_contents/ui/inputContent', ['modelFilter'=>$modelFilter]);
        }
        return $html;
    }
    
    public function registerClientScript() {
        \ui\assets\AssetGrid::register();
        \exp\exp_contents\ui\AssetInputContent::register();
        RC::app()->document->setTextCss('#modal-contents-grid .maze-grid-content tr:hover{ background-color: #ffe4af; cursor: pointer;}');
    }

}
