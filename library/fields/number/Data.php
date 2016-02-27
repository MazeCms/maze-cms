<?php

namespace lib\fields\number;

class Data extends \maze\fields\BaseDataField{
    
   /**
    * @var string - заголовок поля
    */
   public $number_value; 
   
   public function init() {
       $settings = $this->getField()->settings;
       if($this->number_value !== null && is_numeric($this->number_value)){
           $this->number_value = round($this->number_value, $settings->round);
       }
       
       
   }
   
   public function beforeSave() {
       if(empty($this->number_value)){
           return false;
       }
       return true;
   }
    
   public function fieldRule(){
       
       $settings = $this->getField()->settings;
       $rules = [];
       
       if($settings->required){
          $rules[] =  ['number_value', 'required'];
       }
       $rules[] = ['number_value', 'number', 'min'=>$settings->min,  'max'=>$settings->max];
       return $rules;
      
   }
   
   public function attributeLabels() {
        return[
            "number_value"=>$this->field->title
        ];
    }
}
