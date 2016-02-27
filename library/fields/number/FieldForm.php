<?php
namespace lib\fields\number;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - максимальное значение
     */
    public $max;
    
    /**
     * @var type - минимальное значение
     */
    
    public $min;
    
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    
    
    /**
     * @var int - точность числа
     */
    public $round;

    public function rules() {
      return [
           [['max', 'min', 'round'], 'required'],
           [['max', 'min', 'round'], 'number', 'min'=>0],
           ['required', 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "max"=>Text::_("LIB_FIELDS_NUMBER_SETTINGS_MAX_LABEL"),
            "min"=>Text::_("LIB_FIELDS_NUMBER_SETTINGS_MIN_LABEL"),
            "required"=>Text::_("LIB_FIELDS_NUMBER_SETTINGS_REQUIRED_LABEL"),
            "round"=>Text::_("LIB_FIELDS_NUMBER_SETTINGS_ROUND_LABEL")
        ];
    }
    
}
