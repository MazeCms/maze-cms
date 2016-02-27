<?php

namespace lib\fields\number;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'number';
        $this->locked = 0;
    }
    
    public function getScheme(){
        return [
            'number_value'=>"decimal(11) DEFAULT NULL COMMENT 'Значение числового поля'"
        ];
    }
}
