<?php

namespace admin\expansion\exp_constructorblock\sort\random;

use RC;
use maze\base\Model;
use admin\expansion\exp_constructorblock\model\BaseSort;
use maze\db\Expression;

class Params extends BaseSort {

    public function buildQuery($query, $table, $id_name, $expansion, $bundle) {
     
        $query->orderBy(new Expression("RAND()"));
        
    }

}
