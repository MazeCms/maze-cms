<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\Roles;
use maze\table\Privates;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class RolePrivate extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%role_private}}';
    }
    
     public function rules() {
        return [
            [["id_role", "id_priv"], "required"],
        ];
    }
    
    public function getRoles()
    {
        return $this->hasOne(Roles::className(), ['id_role'=>'id_role']);
    }
    
    public function getPrivates()
    {
        return $this->hasMany(Privates::className(), ['id_priv'=>'id_priv']);
    }
    


}
