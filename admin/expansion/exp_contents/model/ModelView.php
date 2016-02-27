<?php

namespace exp\exp_contents\model;


use Text;
use RC;
use maze\base\Model;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use maze\helpers\ArrayHelper;
use maze\table\ContentTypeView;
use maze\table\ContentTypeViewGrid;

class ModelView extends Model {

    public $bundle;
    
    public function getDisableSetting($field){
        $view =  FieldHelper::listFieldView($field);
        $result = [];
        foreach($view as $k=>$v){
           $conf = FieldHelper::getInfoView($field, $k);
           if(!$conf->getParams()){
              $result[] =  $k;
           }
        }
        
        return $result;
    }
    
   

    

}
