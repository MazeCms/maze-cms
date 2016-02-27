<?php
namespace lib\fields\images\view\resize;

use RC;
use maze\base\Model;
use maze\helpers\StringHelper;

class Params extends Model{
   

    public $width;
    
    public $height;

    public function rules() {
      return [
          [['width', 'height'],'required'],
          [['width', 'height'], 'number', 'min'=>1]
      ];
    }
    
    
    
}
