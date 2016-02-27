<?php
namespace lib\fields\body;

use RC;
use Text;
use maze\base\Model;

class FieldForm extends Model{
   
    /**
     * @var int - поле является обязательным
     */
    public $reqfull = 1;
    
    /**
     * @var int - поле является обязательным
     */
    
    public $reqprev = 1;
    
    /**
     * @var array - фильтр вывода доступный
     */
    public $filter;  
   
    /**
     * @var int - фильтр вывода по умолчанию
     */
    public $filterDefault = 'fullhtml';
    
    /**
     * @var string - фильтр тегов для удаления
     */
    public $listtag = 'script, style, iframe';


    public function rules() {
      return [
           [['reqfull', 'reqprev', 'filter', 'filterDefault'], 'required'],
           [['reqfull', 'reqprev'], 'boolean'],
           ['filterDefault', 'string'],
           ['listtag', 'safe']
      ];
    }
    
    public function attributeLabels() {
        return[
            "reqfull"=>Text::_("LIB_FIELDS_BODY_FULL_LABEL"),
            "reqprev"=>Text::_("LIB_FIELDS_BODY_PREVTEXT_LABEL"),
            "filter"=>Text::_("LIB_FIELDS_BODY_FILTER_LABEL"),
            "filterDefault"=>Text::_("LIB_FIELDS_BODY_FILTERDEFAULT_LABEL")
        ];
    }
    
}
