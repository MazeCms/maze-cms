<?php

namespace admin\expansion\exp_constructorblock\model;

use maze\base\Model;
use Text;
use RC;

class BaseSort extends Model {
    
    /**
     * @var string -  имя таблицы
     */
    public $table;
    
    public $field;
    
    public $order;
    
    public $type;

    public function rules() {
      return [
          [['table', 'field', 'order', 'type'], 'required']
      ];
    }
    
    public function buildQuery($query, $tableSelect, $pkey, $expansion, $bundle){
                    
    }
}
