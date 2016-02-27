<?php
namespace lib\fields\file\view\link;

use RC;
use maze\base\Model;

class Params extends Model{
   
    /**
     * @var string - css класс 
     */
    public $cssClass;
    
    /**
     * @var int показывать размер файла
     */
    public $showSize = 1;
    
    /**
     * @var string - формат размера
     */
    public $formatSize = 'M';
    
    
    public function rules() {
      return [
           [['showSize', 'formatSize'], 'required'],
           ['cssClass', 'string']
      ];
    }

    
}
