<?php

namespace admin\expansion\exp_constructorblock\sort\defaults;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseSort;

class Params extends BaseSort {

    public function buildQuery($query, $tableSelect, $pkey, $expansion, $bundle) {
        $order = SORT_ASC;

        if ($this->order == 'DESC')
            $order = SORT_DESC;

        $sortParam[$this->table . '.' . $this->field] = $order;
        $query->addOrderBy($sortParam);
    }

}
