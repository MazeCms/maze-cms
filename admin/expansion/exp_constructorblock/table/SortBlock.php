<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace admin\expansion\exp_constructorblock\table;

use maze\table\Expansion;
use maze\db\Expression;
use maze\helpers\ArrayHelper;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class SortBlock extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%constructorblock_sort}}';
    }
    public function rules() {
        return [
            [['code', 'expansion', 'bundle', 'type', 'field', 'table', 'order', 'filter'], 'required'],
            [['label'], 'safe']
        ];
    }


}
