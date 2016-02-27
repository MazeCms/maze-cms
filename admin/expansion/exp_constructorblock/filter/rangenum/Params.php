<?php
namespace admin\expansion\exp_constructorblock\filter\rangenum;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $number;
    
    public function rules() {
      return [
          ['number', 'required'],
          ['field', 'string'],
          ['table', 'safe']
      ];
    }
    
    public function buildQuery($query, $table, $id_name) {         
        $number = preg_split("/,[\s]+|,/s", $this->number);         
        $params[$this->table.'.'.$this->field] = $number;
        $query->andFilterWhere($params);
    }
    
}
