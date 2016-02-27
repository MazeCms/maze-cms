<?php

namespace lib\fields\text;

class Data extends \maze\fields\BaseDataField{
    
   /**
    * @var string - заголовок поля
    */
   public $text_value; 
    
   public function fieldRule(){
       
       $settings = $this->getField()->settings;
       $rules = [];
       
       if($settings->required){
          $rules[] =  ['text_value', 'required'];
       }
       $rules[] = ['text_value', 'string', 'min'=>$settings->min,  'max'=>$settings->max];
       return $rules;
      
   }
   
   public function beforeSave() {
       if(empty($this->text_value)){
           return false;
       }
       return true;
   }
   
   public function attributeLabels() {
        return[
            "text_value"=>$this->field->title
        ];
    }
}
