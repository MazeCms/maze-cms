<?php

namespace exp\exp_admin\form;

use maze\base\Model;

class Desktop extends Model {

    public $title;
    
    public $description;
    
    public $defaults;
    
    public $colonum;
    
    public $width;
    
    public $id_des;
    
    public $param;
    
    public function rules() {
        return [
            ["title", "required"],
            ['title', 'string', 'max'=>255, 'skipOnEmpty'=>false],
            ['defaults', 'in', 'range'=>[0, 1], 'allowArray'=>true, 'skipOnEmpty'=>false],
            ['defaults', 'default', 'value'=>1],
            [['description','colonum', 'width', 'id_des'], 'safe']

        ];
    }
   
    public function afterValidate()
    {
        $this->param = array('colonum'=>$this->colonum,'width'=>$this->width);
    }
    public function attributeLabels() {
        return[
            "title" => \Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_TITLE"),
            "description" => \Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_DES"),
            "defaults" => \Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_DEFAULTS"),
            "colonum" => \Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_SIZE"),
            "width" => \Text::_("EXP_ADMIN_FORM_ADDDESCKTOP_COLONUM")
        ];
    }

}
