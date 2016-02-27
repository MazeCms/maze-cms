<?php

namespace maze\fields;

use RC;
use maze\helpers\Html;
use maze\helpers\Json;

abstract class BaseWidget extends \maze\base\Object{

    /**
     * @var int $counter - счетчик объектов BaseWidget
    */
    public static $counter = 0;    
    /**
     * @var array $stack - копилка классов BaseWidget
    */
    public static $stack = [];
    
    /**
     * @var array -  настройки поля при мультивыборе 
     */
    public $clientOptions = [];
    
    /**
     * @var string - id элемента обертки 
     */
    public $wrapp;

    /**
     * @var maze\fields\BaseField -  экземпляр объекта поля 
     */
    public $field;
    
    /**
     * @var array|maze\fields\BaseDataField  - модель данных поля
     */
    public $data;
    
    
    /**
     * @var ui\form\FormBuilder - экземпляр объекта формы
     */
    public $form;
  
    public static function element($config = []) {
        ob_start();
        ob_implicit_flush(false);
        if(!isset($config['class'])){
            $config['class'] = get_called_class();       
        }
          
        $widget = \RC::createObject($config);
        
        $out = $widget->run();
        
        if($widget->wrapp && $widget->clientOptions){
            $widget->clientOptions['maxField'] = $widget->field->many_value;
            $text = '$("#' . $widget->wrapp . '").fieldBuilder(' . Json::encode($widget->clientOptions, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES) . ')';
            RC::app()->document->setTextScritp($text,['wrap'=>\Document::DOCREADY]);
        }
        return ob_get_clean() . $out;
    }
    

    public function getInputName($attribute){
        return Html::getInputName($this->data, $attribute);
    }
    
    public function getValue($attribute){        
        return Html::getAttributeValue($this->data, $attribute);
    }

    public function getId($attribute) {
        return Html::getInputId($this->data, $attribute);
    }
    
    /**
     * 
     * @param type $view -  имя вида
     * 
     * переопределение шаблона
     * путь 1 шаблон/views/expansion/exp_расширение/fields/type-widgetName-bundle-$view
     * путь 2 шаблон/views/expansion/exp_расширение/fields/type-widgetName-$view
     * путь 3 шаблон/views/expansion/fields/type-widgetName-$view
     * путь 4 library/fields/type/widget/widgetName/$view
     * 
     * @param type $vars - ['var'=>'value']
     * @return string
     */
    public function render($view, $vars = []){
        $viewObj = RC::app()->view;
        
        $paths = [
            '@tmp/'.$viewObj->theme->name.'/views/expansion/exp_'.$this->field->expansion.'/fields/'.$this->field->type.'-'.$this->field->widget_name.$this->field->bundle.'-'.$view,
            '@tmp/'.$viewObj->theme->name.'/views/expansion/exp_'.$this->field->expansion.'/fields/'.$this->field->type.'-'.$this->field->widget_name.'-'.$view,
            '@tmp/'.$viewObj->theme->name.'/views/fields/'.$this->field->type.'-'.$this->field->widget_name.'-'.$view,
            '@lib/fields/'.$this->field->type.'/widget/'.$this->field->widget_name.'/'.$view
        ];
        
        foreach($paths as $path){
            if($viewObj->hasView($path)){
                $view = $path;
                break;
            }
        }
        
       return $viewObj->render($view, $vars);
    }

    public function run(){
        
    }

}
