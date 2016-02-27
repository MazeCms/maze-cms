<?php
namespace lib\fields\body\widget\body;

use RC;
use maze\base\Model;

class WidgetForm extends Model{
   
    /**
     * @var int использовать редактор для анонса
     */
    public $enableprev = 0;
    
    /**
     * @var int использовать редактор для полного текста
     */
    public $enablefull = 1;
    
    public function rules() {
      return [
          [['enableprev', 'enablefull'], 'required'],
          [['enableprev', 'enablefull'], 'boolean']
      ];
    }
    
}
