<?php
namespace lib\fields\text;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - максимальная длина поля
     */
    public $max = 255;
    
    /**
     * @var type - минимальная длина поля
     */
    
    public $min = 1;
    
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    
   
    
    public function rules() {
      return [
           [['max', 'min'], 'required'],
           [['max', 'min'], 'number', 'min'=>1, 'max'=>255],
           ['required', 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "max"=>Text::_("Максимальная длина строки"),
            "min"=>Text::_("Минимальная длина строки"),
            "required"=>Text::_("Поле является обязательным")
        ];
    }
    
}
