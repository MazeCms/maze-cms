<?php

namespace lib\fields\term;

use maze\table\DictionaryTerm;
use maze\db\Query;
use maze\fields\FieldHelper;

class Data extends \maze\fields\BaseDataField{
    
   /**
    * @var string - заголовок поля
    */
   public $term_id; 
    
   public function fieldRule(){
       
       $settings = $this->getField()->settings;
       $rules = [];
       
       if($settings->required){
          $rules[] =  ['term_id', 'required'];
       }
       $rules[] = ['term_id', 'number'];
       return $rules;
      
   }
   
   public function getTermTitle(){
       $dict = $this->getField()->settings->dictionary;
       
       $title = '';
       $filed = FieldHelper::find(['{{%field_exp}}.expansion'=>'dictionary', '{{%field_exp}}.bundle'=>$dict, '{{%fields}}.type'=>'title']);
       if($filed){
          $data =  $filed->findData(['entry_id'=>$this->term_id]);
          if($data){
              $title = end($data);
              $title = $title['title_value'];
          }
       }
       
       return $title;
   }


   public function attributeLabels() {
        return[
            "term_id"=>$this->field->title
        ];
    }
}
