<?php
namespace admin\expansion\exp_constructorblock\filter\title;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;
use maze\table\FieldExp;

class Params extends BaseFilter{
   

    public $title_value;
    
    public $like;
    
    public function rules() {
      return [
          ['title_value', 'required'],
          ['title_value', 'string'],
          ['like', 'boolean'],
          ['field', 'string'],
          ['table', 'safe']
      ];
    }
    
    public function buildQuery($query, $table, $id_name) {
       
        $query->leftJoin($this->table, $this->table.'.entry_id = '.$table.'.'.$id_name);
        if($this->like){
             $params[$this->table.'.title_value'] = $this->title_value;
            $query->andFilterWhere($params);
        }else{
            $query->andFilterWhere(['like', $this->table.'.title_value', $this->title_value]);
        }
    }
    

    
}
