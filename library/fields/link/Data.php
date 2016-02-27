<?php

namespace lib\fields\link;

use maze\validators\FileValidator;
use maze\upload\UploadedPath;

class Data extends \maze\fields\BaseDataField {

    /**
     * @var string - url адрес ссылки
     */
    public $link_url;

    /**
     * @var string - Название ссылки
     */
    public $link_label;



    public function fieldRule() {

        $settings = $this->getField()->settings;
        $rules = [];

        if ($settings->required) {
            $rules[] = ['link_url', 'required'];
            $rules[] = ['link_label', 'required'];
        }

        $rules[] = ['link_url', 'string'];
        $rules[] = ['link_label', 'string'];
        return $rules;
    }



    public function beforeSave() {
        if(empty($this->link_url)){
           $this->link_url = null;
           return false;
        }

        return true;
    }

    
    public function attributeLabels() {
        return[
            "link_url" => $this->field->title
        ];
    }

}
