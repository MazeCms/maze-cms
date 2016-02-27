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
class Role extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%elfinder_role}}';
    }
    
    public function rules() {
        return [];
    }
    
}
