<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class FiltersFields extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%filters_fields}}';
    }
    
    public function beforeSave($insert) {
        parent::beforeSave($insert);
        
       if($this->datavalue)
       {
            $this->datavalue = serialize( $this->datavalue); 
       }
       return true;
    }
    public function afterFind() {
        parent::afterFind();
        
        if($this->datavalue)
        {
            $this->datavalue = unserialize($this->datavalue);
        }
    }

}
