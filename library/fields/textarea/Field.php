<?php

namespace lib\fields\textarea;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'textarea';
        $this->locked = 0;
    }
    
    public function getScheme(){
        return [
            'text_value'=>"LONGTEXT DEFAULT NULL COMMENT 'Текст'"
        ];
    }
}
