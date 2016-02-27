<?php
namespace lib\fields\body\view\prev;

use RC;
use maze\base\Model;
use maze\helpers\StringHelper;

class Params extends Model{
   
    /**
     * @var int длина поля
     */
    public $length = 50;
    
    public $prefix = '...';
    
    public $stripTag = 1;

    public function rules() {
      return [
          [['length', 'stripTag'],'required'],
          ['prefix', 'safe']
      ];
    }
    
    public function getCutText($text){
        if($this->stripTag){
            $text = strip_tags($text);
        }
        return StringHelper::truncate($text, $this->length, $this->prefix);   
    }

    
}
