<?php
namespace lib\fields\title;

use RC;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int длина поля
     */
    public $length = 255;
    
    public $required = 1;
    
    public function rules() {
      return [
           ['length', 'required'],
           ['length', 'number', 'min'=>1, 'max'=>255],
           ['required', 'boolean']
      ];
    }
    
}
