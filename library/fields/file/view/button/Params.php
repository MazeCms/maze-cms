<?php
namespace lib\fields\file\view\button;

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
    
    
   /**
     * @var string - действия при клике
     */
    public $onclick = 'window.location.href = this.getAttribute("data-href"); return false;';
    
    public function rules() {
      return [
           [['showSize', 'formatSize'], 'required'],
           [['cssClass', 'onclick'], 'string']
      ];
    }

    
}
