<?php

namespace exp\exp_dictionary\form;

use maze\base\Model;
use Text;

class FormType extends Model {


    public $multilang;
    
    public $enabled;
    
    public $title;
    
    public $length;


    public function rules() {
        return [
            [['multilang', 'enabled', 'title', 'length'], "required"],
            ['length', 'number', 'min'=>1, 'max'=>255],
            [['multilang', 'enabled'], 'boolean']
        ];
    }
      
    
    public function attributeLabels() {
        return[
            "multilang"=>Text::_("EXP_DICTIONARY_LABEL_MULTILANG"),
            "enabled"=>Text::_("EXP_DICTIONARY_LABEL_ENABLED"),
            "title"=>Text::_("EXP_DICTIONARY_LABEL_TITLE"),
            "length"=>Text::_("EXP_DICTIONARY_LABEL_LENGTH")
        ];
    }

}
