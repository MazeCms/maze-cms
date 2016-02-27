<?php
namespace admin\expansion\exp_constructorblock\filter\term;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $term_id;
    
    public function rules() {
      return [
          ['term_id', 'required'],
          ['table', 'safe'],
          ['field', 'string'],
          ['term_id', 'validTermID']
      ];
    }
    
    public function validTermID($attribute, $param) {
        $this->term_id = array_filter($this->term_id, function($val){
            return !empty($val);
        });
        if(empty($this->term_id)){
            $this->addError($attribute, 'Термин  словаря не может быть пустым');
        }
    }
    
    public function buildQuery($query, $table, $id_name) {
        $query->leftJoin($this->table, $this->table.'.entry_id = '.$table.'.'.$id_name);
        
        $params[$this->table.'.term_id'] = $this->term_id;
        $query->andFilterWhere($params);
    }

    
}
