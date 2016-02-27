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
class FilterBlock extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%constructorblock_filter}}';
    }
    public function rules() {
        return [
            [['code', 'expansion', 'bundle', 'type', 'field', 'filter'], 'required'],
            [['label', 'queryFilter'], 'safe']
        ];
    }
    
    public function beforeSave($insert) {
        if (!empty($this->queryFilter)) {
            $this->queryFilter = json_encode($this->queryFilter, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }   
        return true;
    }

    public function afterFind() {
        if (!empty($this->queryFilter)) {
            $this->queryFilter = json_decode($this->queryFilter, true);
        }
    }


}
