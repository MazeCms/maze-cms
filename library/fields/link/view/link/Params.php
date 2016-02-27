<?php
namespace lib\fields\link\view\link;

use RC;
use maze\base\Model;

class Params extends Model{
   
    /**
     * @var string - css класс 
     */
    public $cssClass;
    
    /**
     * @var int - использовать js
     */
    public $onclick = 0;
    
    /**
     * @var string - обработчик события клика по ссылке
     */
    public $handler = 'window.location.href = this.getAttribute("data-href"); return false;';


    public function rules() {
      return [
           [['onclick'], 'required'],
           [['cssClass', 'handler'], 'string']
      ];
    }

    
}
