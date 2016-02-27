<?php

namespace exp\exp_widget\form;

use maze\base\Model;

class FormMoving extends Model {


    public $id_tmp;
    
    public $position;
  
    public function rules() {
        return [
            [['position', 'id_tmp'], "required"],          
            [['id_tmp'], 'number']
        ];
    }
      
    
    public function attributeLabels() {
        return[
            "position"=>\Text::_("EXP_WIDGET_FORM_LABEL_POSITION"),
            "id_tmp"=>\Text::_("EXP_WIDGET_FORM_LABEL_TMP")
        ];
    }

}
