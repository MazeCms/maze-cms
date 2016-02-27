<?php

namespace lib\fields\link;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'link';
        $this->locked = 0;
    }
    
   
    public function getScheme(){
        return [
            'link_url'=>"varchar(255) DEFAULT NULL COMMENT 'Название ссылки'",
            'link_label'=>"varchar(4000) DEFAULT NULL COMMENT 'url адрес ссылки'"
        ];
    }
}
