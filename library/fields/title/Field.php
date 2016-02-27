<?php

namespace lib\fields\title;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        
        $this->type = 'title';
        
        $this->locked = 1;
    }
    
    public function getScheme(){
        return [
            'title_value'=>"varchar(255) DEFAULT NULL"
        ];
    }
}
