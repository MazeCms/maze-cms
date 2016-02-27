<?php

namespace exp\exp_contents\model;

use maze\helpers\Html;
use RC;

class BehaviorField extends \maze\base\Behavior {

    public $field;
    
    public $view;
    
    public $data;

    public function init() {
        if ($this->field->many_value != 0 && $this->field->many_value > 1) {
            $start = $this->view->multiple_start === null || $this->view->multiple_start > count($this->field->data) ? 0 : $this->view->multiple_start;
            if ($this->view->multiple_size !== null) {
                if ($this->view->multiple_size > count($this->field->data) - $start) {
                    $end = null;
                } else {
                    $end = $this->view->multiple_size;
                }
            }
            if ($start !== null && isset($end) && $end) {
                $this->data = array_slice($this->field->data, $start, $end);
            } elseif ($start) {
                $this->data = array_slice($this->field->data, $start);
            }
        }
        
    }
    

    public function getRenderField() {       
        $content = $this->field->render($this->view->field_view, $this->view->field_view_param, $this->data);
        RC::getPlugin("system")->triggerHandler("afterContentFieldRender", [$this, &$content]);
        return $content;
    }
    
    protected function getTagName(){
        return $this->view->tag_wrapper ? $this->view->tag_wrapper : null;
    }

    public function getRenderLabel(){
        return $this->view->show_label ? $this->field->title : '';
    }
    
    public function getClassWrapp(){
        $class[] = 'field-view-'.$this->view->view_name;
        
        if($this->view->class_wrapper){
            $class[] = $this->view->class_wrapper;
        }
        $class[] = 'field-entry-type-'.$this->view->entry_type;
        $class[] = 'field-bundle-'.$this->view->bundle;
        $class[] = 'field-expansion-'.$this->view->expansion;
        $class[] = 'field-name-'.$this->field->field_name;
        return $class;
    }

    public function getBeginWrap(array $option = []){
        $class = $this->getClassWrapp(); 
        $options['class'] = implode(' ', $class);
        $options = array_merge($options, $option);
        if(!$this->getTagName()) return '';
        return Html::beginTag($this->getTagName(), $options);
    }
    
    public function getEndWrap(){
        if(!$this->getTagName()) return '';
        return Html::endTag($this->getTagName());
    }
    

}
