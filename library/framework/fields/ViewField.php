<?php

namespace maze\fields;

use RC;
use Text;
use maze\helpers\Html;
use maze\helpers\Json;

class ViewField extends \maze\base\Object{

    /**
     * @var maze\base\Model -  настройки вида поля 
     */
    public $param;
    
    /**
     * @var maze\fields\BaseField -  экземпляр объекта поля 
     */
    public $field;
    
    /**
     * @var maze\fields\BaseDataField  - модель данных поля
     */
    public $data;
    
    /**
     * @var string  - имя вида поля
     */
    public $viewName;


    /**
     * переопределение шаблона
     * 
     * путь 1 шаблон/views/expansion/exp_расширение/fields/view/type-viewName-bundle-index
     * путь 2 шаблон/views/expansion/exp_расширение/fields/view/type-viewName-index
     * путь 3 шаблон/views/expansion/fields/view/type-viewName-index
     * путь 4 library/fields/type/view/viewName/index
     * 
     * @return string
     */
    public function render(){
        $viewObj = RC::app()->view;
        $view = 'index';
        $paths = [
            '@tmp/'.$viewObj->theme->name.'/views/expansion/exp_'.$this->field->expansion.'/fields/view/'.$this->field->type.'-'.$this->viewName.'-'.$this->field->field_name.'-'.$this->field->bundle.'-'.$view,
            '@tmp/'.$viewObj->theme->name.'/views/expansion/exp_'.$this->field->expansion.'/fields/view/'.$this->field->type.'-'.$this->viewName.'-'.$this->field->bundle.'-'.$view,
            '@tmp/'.$viewObj->theme->name.'/views/expansion/exp_'.$this->field->expansion.'/fields/view/'.$this->field->type.'-'.$this->viewName.'-'.$view,
            '@tmp/'.$viewObj->theme->name.'/views/fields/view/'.$this->field->type.'-'.$this->viewName.'-'.$view,
            '@lib/fields/'.$this->field->type.'/view/'.$this->viewName.'/'.$view
        ];
        
        foreach($paths as $path){
            if($viewObj->hasView($path)){
                $view = $path;
                break;
            }
        }
        
       $this->beforeRender(); 
       
       RC::getPlugin("system")->triggerHandler("afertFieldRender", [$this->data, $this]); 
       
       return $viewObj->render($view, [
           'data'=>$this->data,
           'param'=>$this->param,
           'view'=>$this
        ]);
    }
    
    public function beforeRender(){
        
    }


}
