<?php

namespace lib\fields\images;

use Text;
use maze\validators\ImageValidator;
use maze\upload\UploadedPath;

class Data extends \maze\fields\BaseDataField {

    /**
     * @var string - Текст полностью
     */
    public $path_image;

    public function fieldRule() {

        $settings = $this->getField()->settings;
        $rules = [];

        if ($settings->required) {
            $rules[] = ['path_image', 'required'];
        }
        $rules[] = ['path_image', 'validImages'];

        return $rules;
    }

    public function validImages($attribute, $param) {

        if ($this->hasErrors())
            return false;

       
        $settings = $this->getField()->settings;
        $attr = '@root/'.$this->$attribute;
        $img = new ImageValidator([
            'minWidth' => $settings->minWidth,
            'maxWidth' => $settings->maxWidth,
            'minHeight' => $settings->minHeight,
            'maxHeight' => $settings->maxHeight,
            'types' => $settings->types
        ]);
        $err = null;
        $val = UploadedPath::getInstancePath($attribute, $attr);
        
     
        if (!$img->validate($val, $err)) {
            $this->addError($attribute, $err);
        }
    }
    
    public function beforeSave() {
        if(empty($this->path_image)){
           $this->path_image = null;
           return false;
        }

        return true;
    }

    public function attributeLabels() {
        return[
            "path_image" => $this->field->title
        ];
    }

}
