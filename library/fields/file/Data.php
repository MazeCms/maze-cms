<?php

namespace lib\fields\file;

use maze\validators\FileValidator;
use maze\upload\UploadedPath;

class Data extends \maze\fields\BaseDataField {

    /**
     * @var string - Путь к фалу
     */
    public $path_file;

    /**
     * @var string - Название файла
     */
    public $label_file;

    /**
     * @var string - Тип файла
     */
    public $type_file;

    /**
     * @var int - Размер файла в байтах
     */
    public $size_file;

    public function fieldRule() {

        $settings = $this->getField()->settings;
        $rules = [];

        if ($settings->required) {
            $rules[] = ['path_file', 'required'];
        }

        $rules[] = ['label_file', 'string'];
        $rules[] = ['path_file', 'validFile'];
        return $rules;
    }

    public function validFile($attribute, $param) {

        if ($this->hasErrors())
            return false;


        $settings = $this->getField()->settings;
        $attr = '@root/' . $this->$attribute;
        $img = new FileValidator([
            'minSize' => $settings->minSize,
            'maxSize' => $settings->maxSize,
            'types' => $settings->types
        ]);
        $err = null;
        $val = UploadedPath::getInstancePath($attr, $attr);


        if (!$img->validate($val, $err)) {
            $this->addError($attribute, $err);
        }
    }

    public function beforeSave() {
        if(empty($this->path_file)){
           $this->path_file = null;
           return false;
        }
        $attr = '@root/' . $this->path_file;
        $val = UploadedPath::getInstancePath($attr, $attr);
        $this->type_file = $val->type;
        $this->size_file = $val->size;
        if(empty($this->label_file)){
            $this->label_file = $val->getBaseName();
        }
        
        
        return true;
    }

    public function sizeToBytes($sizeStr = 'M') {
       
        switch ($sizeStr) {
            case 'M':
                $val =  (int) $this->size_file / 1048576;
                break;
            case 'K':
                $val =  (int) $this->size_file / 1024;
                 break;
            case 'G':
                $val =  (int) $this->size_file / 1073741824;
                 break;
            default:
                $val =  (int) $this->size_file;
                 break;
        }
        
        return round($val, 2) .' '.$sizeStr;
    }

    
    public function attributeLabels() {
        return[
            "path_file" => $this->field->title
        ];
    }

}
