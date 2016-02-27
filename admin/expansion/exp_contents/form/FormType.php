<?php

namespace exp\exp_contents\form;

use maze\base\Model;
use Text;

class FormType extends Model {


    public $multilang;
    
    public $enabled;
    
    public $title;
    
    public $home;
    
    public $length;


    public function rules() {
        return [
            [['multilang', 'enabled', 'title', 'home', 'length'], "required"],
            ['length', 'number', 'min'=>1, 'max'=>255],
            [['multilang', 'enabled', 'home'], 'boolean']
        ];
    }
      
    
    public function attributeLabels() {
        return[
            "multilang"=>Text::_("EXP_CONTENTS_LABEL_MULTILANG"),
            "enabled"=>Text::_("EXP_CONTENTS_LABEL_ENABLED"),
            "title"=>Text::_("EXP_CONTENTS_LABEL_TITLE"),
            "home"=>Text::_("EXP_CONTENTS_LABEL_HOME"),
            "length"=>Text::_("EXP_CONTENTS_LABEL_LENGTH")
        ];
    }

}
