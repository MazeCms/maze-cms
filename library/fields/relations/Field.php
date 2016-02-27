<?php

namespace lib\fields\relations;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'relations';
        $this->locked = 0;
    }
    
    public function getScheme(){
        return [
            'contents_id'=>"INT(11) DEFAULT NULL COMMENT 'id материала'"
        ];
    }
}
