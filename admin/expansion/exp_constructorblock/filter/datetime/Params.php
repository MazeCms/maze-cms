<?php
namespace admin\expansion\exp_constructorblock\filter\datetime;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $start;
    
    public $end;

    public function rules() {
      return [
          [['start', 'end'],'required'],
          [['start', 'end'], 'date', 'format'=>'Y-m-d H:i:s'],
          ['field', 'string'],
          ['table', 'safe']
      ];
    }

    public function buildQuery($query) {
        
    }
    
}
