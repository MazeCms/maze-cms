<?php

namespace admin\expansion\exp_constructorblock\model;

use maze\base\Model;
use Text;
use RC;
use maze\fields\FieldHelper;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use admin\expansion\exp_constructorblock\table\Block;
use admin\expansion\exp_constructorblock\table\FilterBlock;
use admin\expansion\exp_constructorblock\table\ViewBlock;
use admin\expansion\exp_constructorblock\table\SortBlock;

class ModelContent extends Model {

    public $contents;

    public $code;
    
    public $pkey;

    protected $fields;
    
    protected $viewfield;

    public static function createModel($contents) {
        $result = [];
        if(!$contents) return false;
        
        if(ArrayHelper::isIndexed($contents)){
            foreach($contents as $cont){
                $result[] = static::createModel($cont);
            }
        }else{
            $model = new self($contents);
 
            $fields = $model->getFields();
        
            foreach($fields as $field){
               $field->findData(['entry_id'=>$model->contents[$model->pkey]]);
            }
            
            $result = $model;
        }
        
        return $result;
    }
    
    
    public function getContents() {
        return $this->contents;
    }
    
    public function getId() {
        return $this->contents[$this->pkey];
    }
    
    public function getFields() {
        if ($this->getContents() && $this->fields == null) {
            $this->fields = FieldHelper::findAll(['expansion' => $this->getContents()['expansion'], 'bundle' => $this->getContents()['bundle'], 'active'=>1]);
        }
        return $this->fields;
    }
    
    
    public function getField($name) {
        $result = null;
        foreach ($this->getFields() as $field) {
            if ($field->field_name == $name) {
                $result = $field;
                break;
            }
        }
        return $result;
    }
    
    public function getTitle(){
      $field =  $this->getField('title');
      if($field){
          if($field->data){
              return $field->data[0]->title_value;
          }
      }
    }
    
    public function getFieldDyID($id) {
        $fields = $this->getFields();
        $result = null;
        foreach($fields as $field){
            if($field->field_exp_id == $id){
                $result = $field;
                break;
            }
        }
        return $result;
    }
    
    public function getViewField() {
        if($this->viewfield == null){
            $contents = $this->getContents();
            $views = RC::getDb()->cache(function($db){ 
                return ViewBlock::find()->where(['code' => $this->code, 'enabled' => 1])->orderBy('sort')->all();
            }, null, 'exp_constructorblock');
            
           foreach ($views as $key => $view) {
           if(($field = $this->getFieldDyID($view->field_exp_id))){
                $view->attachBehavior($key, ['class' => 'admin\expansion\exp_constructorblock\model\BehaviorField', 'view' => $view, 'field' => clone $field, 'code'=>$this->code]);
            } else {
                unset($views[$key]);
            }
        }
            
            $this->viewfield = $views;
        }
        

        return $this->viewfield;
    }
    
 

    

}
