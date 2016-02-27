<?php

namespace admin\expansion\exp_constructorblock\sort\title;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseSort;
use maze\db\Query;
use maze\table\FieldExp;

class Params extends BaseSort {

    public function buildQuery($query, $table, $id_name, $expansion, $bundle) {
        $order = SORT_ASC;

        if ($this->order == 'DESC')
            $order = SORT_DESC;
       
        $field = FieldExp::find()
                ->from(['fe'=>FieldExp::tableName()])
                ->joinWith(['typeFields'])
                ->where(['fe.expansion'=>$expansion, 'fe.bundle'=>$bundle, 'f.type'=>'title'])
                ->one();
        
       
        $sortParam[$this->table.'.title_value'] = $order;
        $query->leftJoin($this->table, $this->table.'.entry_id = '.$table.'.'.$id_name);
        $query->andWhere([$this->table.'.field_exp_id'=>$field->field_exp_id]);
        $query->addOrderBy($sortParam);

    }

}
