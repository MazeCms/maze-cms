<?php

namespace lib\fields\relations\widget\input;

use RC;
use maze\helpers\Html;
use maze\base\JsExpression;

class Widget extends \maze\fields\BaseWidget {
  
  
   
   public function run(){
      $types = $this->field->settings;
      
      return  $this->render('index', [
                'widget'=>$this, 
                'form'=>$this->form
              ]);
   }
}
