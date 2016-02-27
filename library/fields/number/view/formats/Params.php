<?php
namespace lib\fields\number\view\formats;

use RC;
use maze\base\Model;

class Params extends Model{
   
    /**
     * @var int знаков после запятой
     */
    public $decimals = 0;
    
    /**
     * @var string разделитель десятвок
     */
    public $dec_point = ',';
    
    /**
     * @var string разделитель тысяч
     */
    public $thousands_sep = ' ';
    
    /**
     * @var string префик числа
     */
    public $prefix;
   
    
    public function rules() {
      return [
            [['decimals','thousands_sep', 'dec_point'], 'required'],
            ['decimals', 'number', 'min'=>0, 'max'=>5],
            [['thousands_sep', 'dec_point', 'prefix'], 'string']
      ];
    }
    
    public function getFormatNum($number){
        $result = number_format($number, $this->decimals, $this->dec_point, $this->thousands_sep);
        if($this->prefix){
           $result .= ' '.$this->prefix;
        }
        return $result;
    }

    
}
