<?php

namespace lib\fields\file;

class Field extends \maze\fields\BaseField{
    
    public function init(){
        $this->type = 'file';
        $this->locked = 0;
    }
    
   
    public function getScheme(){
        return [
            'label_file'=>"varchar(255) DEFAULT NULL COMMENT 'Название файла'",
            'path_file'=>"varchar(4000) DEFAULT NULL COMMENT 'Путь к фалу'",
            'type_file'=>"varchar(80) DEFAULT NULL COMMENT 'Тип файла'",
            'size_file' => "INT(11) DEFAULT NULL COMMENT 'Размер файла в байтах'",
        ];
    }
}
