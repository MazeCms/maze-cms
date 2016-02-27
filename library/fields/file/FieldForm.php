<?php
namespace lib\fields\file;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - минимальный размер файла  в байтах
     */
    public $minSize;
    
    /**
     * @var type - максимальный размер файла  в байтах
     */
    
    public $maxSize;
    
    /**
     * @var int - поле является обязательным
     */
    public $required = 1;
    
    /**
     * @var string - разрешенные типы файлов (pdf, jpeg, doc, txt)
     */
    public $types = 'pdf, jpeg, doc, txt';

    public function rules() {
      return [
            [['minSize', 'maxSize', 'required', 'types'], 'required'],
            [['minSize', 'maxSize'], 'number'],
            ['types', 'string'],
            ['required', 'boolean']
      ];
    }
    
    public function attributeLabels() {
        return[
            "maxSize"=>Text::_("LIB_FIELDS_FILE_SETTINGS_MAX_LABEL"),
            "minSize"=>Text::_("LIB_FIELDS_FILE_SETTINGS_MIN_LABEL"),
            "required"=>Text::_("LIB_FIELDS_FILE_SETTINGS_REQUIRED_LABEL"),
            "types"=>Text::_("LIB_FIELDS_FILE_SETTINGS_TYPES_LABEL")
        ];
    }
    
}
