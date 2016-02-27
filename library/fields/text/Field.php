<?php

namespace lib\fields\text;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'text';
        $this->locked = 0;
    }
    
    public function getScheme(){
        return [
            'text_value'=>"varchar(255) DEFAULT NULL COMMENT 'Значение текстового поля'"
        ];
    }
}
