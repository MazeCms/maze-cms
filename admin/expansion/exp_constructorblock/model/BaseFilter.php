<?php

namespace admin\expansion\exp_constructorblock\model;

use maze\base\Model;
use Text;
use RC;

class BaseFilter extends Model {
    
    /**
     * @var string -  имя таблицы
     */
    public $table;
    
    public $field;
    
    public function buildQuery($query, $table, $id_name){
        
    }
}
