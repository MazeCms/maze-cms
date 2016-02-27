<?php
namespace admin\expansion\exp_constructorblock\filter\boolean;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $bool;
   
    public function rules() {
      return [
          ['bool','required'],
          ['bool', 'boolean'],
          ['field', 'string'],
          ['table', 'safe']
      ];
    }
    
    public function buildQuery($query, $table, $id_name) {
        $params[$this->table.'.'.$this->field] = $this->bool;
        $query->andFilterWhere($params);
    }
    
}
