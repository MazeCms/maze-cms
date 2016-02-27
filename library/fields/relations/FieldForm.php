<?php
namespace lib\fields\relations;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    
     /**
     * @var string - тип материалов
     */
    public $contentstype;
    
   

    public function rules() {
      return [
           ['contentstype', 'required'],
           [['required'], 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "required"=>Text::_("Поле является обязательным"),
            "contentstype"=>Text::_("Тип материалов")
        ];
    }
    
}
