<?php
namespace lib\fields\term\view\link;

use RC;
use maze\base\Model;

class Params extends Model{
   
    /**
     * @var int длина поля
     */
    public $url;
   
    
    public function rules() {
      return [
           ['url', 'required']
      ];
    }
    
    public function getRealUrl($data){
        $params = [];
        foreach($data as $name=>$val){
            $params['{'.$name.'}'] = $val;
        }
        return strtr($this->url, $params);
    }

    
}
