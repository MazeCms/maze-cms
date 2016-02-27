<?php

namespace ui\menu;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\assets\AssetSelectTree;

class SelectTree extends Elements {

    public $options;
    public $attribute;
    public $settings = [];
    public $items = [];
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
        $items = $this->items;
        $this->items = [];
        $this->options['options'] =  [];
        $this->options['prompt'] = '...';
        foreach($items as $item)
        {
           if(isset($item['value']) && isset($item['label']))
           {
               $value = $item['value'];
               $this->items[$value] = $item['label'];
           
               $this->options['options'][$value] = [
                    'data-parent'=>(isset($item['parent']) ? $item['parent'] : 0)
                   ];
                  
               if(isset($item['parent']))
               {
                   unset($item['parent']);   
               }
               if(isset($item['icon']))
               {
                   $this->options['options'][$value]['data-icon'] = $item['icon'];
                   unset($item['icon']);   
               }

               unset($item['value']);
               unset($item['label']);
               $this->options['options'][$value] = array_merge($this->options['options'][$value], $item);
               
           }
          
        }
        if(isset($this->settings['url']) && !empty($this->settings['url']))
        {
            $this->settings['url'] = (new \URI($this->settings['url']))->toString();
        }
        $this->settings = array_merge([
            'defaulticon'=>'/library/image/icons/blue-folder-horizontal.png',
            'url'=>'',
            'dataFilter'=>new JsExpression('function(data){return data.html}'),
            'tree'=>[
                'core'=>['strings'=>['Loading ...'=>'Загрузка...']],
                'checkbox'=>['three_state'=>false],
                'plugins'=>[ "wholerow", "checkbox"]
            ]
        ],$this->settings);
        
    }

    public function run() {

        $html = Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        \RC::app()->document->setTextScritp('$( "#'.$this->options['id'] . '" ).mazeSelectTree('.Json::encode($this->settings).');',
            ['wrap'=>\Document::DOCREADY]);
        return $html;
    }
    
    public function registerClientScript() {
        AssetSelectTree::register();
    }

}
