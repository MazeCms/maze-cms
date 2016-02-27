<?php

namespace lib\fields\textarea\widget\input;

use RC;
use maze\helpers\Html;
use maze\base\JsExpression;

class Widget extends \maze\fields\BaseWidget {
  
  
   
   public function run(){
     
     
      return  $this->render('index', [
                'widget'=>$this, 
                'form'=>$this->form
              ]);
   }
}
