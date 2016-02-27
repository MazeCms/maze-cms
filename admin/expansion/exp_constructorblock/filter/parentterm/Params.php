<?php
namespace admin\expansion\exp_constructorblock\filter\parentterm;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $parent;
    
    public function rules() {
      return [
          ['parent', 'required'],
          ['table', 'safe'],
          ['field', 'string'],
          ['parent', 'validTermID']
      ];
    }
    
    public function validTermID($attribute, $param) {
        $this->parent = array_filter($this->parent, function($val){
            return !empty($val);
        });
        if(empty($this->parent)){
            $this->addError($attribute, 'Термин  словаря не может быть пустым');
        }
    }
    
    public function buildQuery($query, $table, $id_name) {
        $params[$this->table.'.'.$this->field] = $this->parent;
        $query->andFilterWhere($params);
    }
    
}
