<?php

namespace lib\fields\body;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'body';
        $this->locked = 0;
    }
    
    public function rules() {
        $rules = parent::rules();
        $rules[] = ['many_value', 'number', 'max'=>1, 'min'=>1];
        return $rules;
    }
    
    public function getScheme(){
        return [
            'text_prev'=>"TEXT DEFAULT NULL COMMENT 'Анонс'",
            'text_full'=>"LONGTEXT DEFAULT NULL COMMENT 'Текст полностью'",
            'text_format'=>"varchar(50) DEFAULT NULL COMMENT 'Формат текста'"
        ];
    }
}
