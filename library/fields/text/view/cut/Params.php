<?php
namespace lib\fields\text\view\cut;

use RC;
use maze\base\Model;

class Params extends Model{
   
    /**
     * @var int длина поля
     */
    public $cut = 50;
   
    
    public function rules() {
      return [
           ['cut', 'required']
      ];
    }

    
}
