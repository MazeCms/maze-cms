<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace lib\fields\body;

use Text;
use RC;

class ViewField extends \maze\fields\ViewField{
    
    public function beforeRender() {
        if($this->viewName =='default'){
            if($this->data){
                RC::getPlugin("content")->triggerHandler("getArticleAfter", [&$this->data->text_full]);
            }
             
        }
        
    }
}
