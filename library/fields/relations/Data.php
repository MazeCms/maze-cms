<?php

namespace lib\fields\relations;

use RC;
use maze\db\Query;

class Data extends \maze\fields\BaseDataField{
    
   /**
    * @var string - заголовок поля
    */
   public $contents_id; 
    
   public function init() {
       $request = RC::app()->request;
         
        if($request->isAjax() && $request->get('clear') == 'ajax' &&
                  $request->get('field_exp_id') == $this->getField()->field_exp_id  && $request->get('fieldrelations')){
            $settings = $this->getField()->settings;
            
            $searchCont =  (new Query())
                    ->select(['c.contents_id', 't.title_value'])
                    ->from(['c' =>'{{%contents}}']) 
                    ->leftJoin(['fe'=>'{{%field_exp}}'], ['and', 'fe.expansion=c.expansion', 'fe.bundle=c.bundle'])
                    ->leftJoin(['t'=>'{{%field_title_title}}'], ['and', 't.entry_id=c.contents_id', 't.field_exp_id=fe.field_exp_id'])
                    ->where(['c.expansion'=>'contents'])
                    ->andWhere(['c.bundle'=>$settings->contentstype])
                    ->andWhere(['fe.field_name'=>'title'])
                    ->andFilterWhere(['like', 't.title_value', $request->get('fieldrelations')])                    
                    ->groupBy('c.contents_id')
                    ->all();
            
            RC::app()->response->format = 'ajax';
            $result = [];
            if($searchCont){
                $result = array_map(function($val){
                    return ['id'=>$val['contents_id'], 'value'=>$val['title_value'], 'label'=>$val['title_value']];
                }, $searchCont);
            }
            RC::app()->response->data = $result;
        }
   }
   public function fieldRule(){
       
       $settings = $this->getField()->settings;
       $rules = [];
       
       if($settings->required){
          $rules[] =  ['contents_id', 'required'];
       }
 
       return $rules;
   }
   
   public function getTitle(){
       $settings = $this->getField()->settings;
        if($this->contents_id){
          $result =  (new Query())
                    ->select(['c.contents_id', 't.title_value'])
                    ->from(['c' =>'{{%contents}}']) 
                    ->leftJoin(['fe'=>'{{%field_exp}}'], ['and', 'fe.expansion=c.expansion', 'fe.bundle=c.bundle'])
                    ->leftJoin(['t'=>'{{%field_title_title}}'], ['and', 't.entry_id=c.contents_id', 't.field_exp_id=fe.field_exp_id'])
                    ->where(['c.expansion'=>'contents'])
                    ->andWhere(['c.bundle'=>$settings->contentstype])
                    ->andWhere(['fe.field_name'=>'title'])
                    ->andWhere(['t.entry_id'=>  $this->contents_id])
                    ->groupBy('c.contents_id')
                    ->one();
          return $result ? $result['title_value'] : null;
        }
       
   }


   public function beforeSave() {
       if(empty($this->contents_id) || !$this->contents_id){
           return false;
       }
       return true;
   }
   
   public function attributeLabels() {
        return[
            "contents_id"=>$this->field->title
        ];
    }
}
