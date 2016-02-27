<?php
namespace admin\expansion\exp_constructorblock\filter\lang;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseFilter;

class Params extends BaseFilter{
   

    public $id_lang;
    
    public function rules() {
      return [
          ['id_lang', 'required'],
          ['id_lang', 'validLangID'],
          ['field', 'string'],
          ['table', 'safe']
      ];
    }
    
    public function validLangID($attribute, $param) {
        $this->id_lang = array_filter($this->id_lang, function($val){
            return $val !== '';
        });
        if(empty($this->id_lang)){
            $this->addError($attribute, 'Язык не может быть пустым');
        }
    }
    
    public function buildQuery($query, $table, $id_name) {
        $params[$this->table.'.'.$this->field] = $this->id_lang;
        $query->andFilterWhere($params);
    }

    
}
