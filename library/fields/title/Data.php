<?php

namespace lib\fields\title;

class Data extends \maze\fields\BaseDataField{
    
   /**
    * @var string - заголовок поля
    */
   public $title_value; 
    
   public function fieldRule(){
       
       $settings = $this->getField()->settings;
       $rules = [];
       
       if($settings->required){
          $rules[] =  ['title_value', 'required'];
       }
       $rules[] = ['title_value', 'string', 'max'=>$settings->length];
       return $rules;
      
   }
   
   public function attributeLabels() {
        return[
            "title_value"=>$this->field->title
        ];
    }
}
