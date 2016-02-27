<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\FiltersFields;
use maze\db\Expression;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Filters extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%filters}}';
    }
    
    
    public function getFields()
    {
        return $this->hasMany(FiltersFields::className(), ['filter_id'=>'filter_id'])->indexBy('field');
    }
    
    public function beforeSave($insert) {
        parent::beforeSave($insert);
        if($this->isNewRecord)
        {             
            $this->id_user = \RC::app()->access->getUid();
            $this->enabled = 1;
            $condition = ['component' => $this->component,
            'code' => $this->code, 'id_user' => \RC::app()->access->getUid()];
            $this->sort = Filters::find()->where($condition)->max('sort')+1;
            $this->create_date = new Expression('NOW()');
        }
        return true;
    }

}
