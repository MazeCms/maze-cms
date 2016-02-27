<?php

namespace lib\fields\title\widget\input;

use RC;

class Widget extends \maze\fields\BaseWidget {
  
   public $color;
    
   public $reqColor;
            
   public function run(){
       return  $this->render('index', [
                'widget'=>$this, 
                'form'=>$this->form
              ]);
   }
}
