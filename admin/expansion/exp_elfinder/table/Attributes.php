<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace exp\exp_elfinder\table;

use maze\db\ActiveRecord;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Attributes extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%elfinder_attributes}}';
    }
    
    public function rules() {
        return [
            [["path_id", "pattern"], "required"],
            [['read', 'write', 'hidden', 'locked'], 'boolean'],
        ];
    }
    
}
