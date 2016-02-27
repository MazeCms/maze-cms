<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\table\Roles;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class AccessRole extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%access_role}}';
    }    
    
    
    public function getRole(){
        return $this->hasOne(Roles::className(), ['id_role' => 'id_role'])->from(['r'=>Roles::tableName()]);
    }
   
}
