<?php
namespace lib\fields\link;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    

    public function rules() {
      return [
            [['required'], 'required'],
            ['required', 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "required"=>Text::_("LIB_FIELDS_LINK_SETTINGS_REQUIRED_LABEL")
        ];
    }
    
}
