<?php
namespace lib\fields\term;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
    
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    /**
     * Данное поле является обязательниым для всех 
     * моделей данного типа ссылающихся на словарь
     * 
     * @var string - bundle словаря
     */
    public $dictionary;

    public function rules() {
      return [
           ['dictionary', 'required'],
           ['required', 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "required"=>Text::_("Поле является обязательным"),
            "dictionary"=>Text::_("Словарь")
        ];
    }
    
}
