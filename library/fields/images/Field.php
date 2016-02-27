<?php

namespace lib\fields\images;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'images';
        $this->locked = 0;
    }
    
    public function rules() {
        $rules = parent::rules();
        $rules[] = ['many_value', 'number', 'min'=>1];
        return $rules;
    }
    
    public function getScheme(){
        return [
            'path_image'=>"varchar(6000) DEFAULT NULL COMMENT 'Путь к изображению'"
        ];
    }
}
