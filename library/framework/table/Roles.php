<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\UserRoles;
use maze\table\RolePrivate;
use maze\helpers\ArrayHelper;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Roles extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%roles}}';
    }
    
    public function rules() {
        return [
            ["name", "required"],
            ['description', 'safe']
        ];
    }
    
    public function getUserRoles()
    {
        return $this->hasOne(UserRoles::className(), ['id_role'=>'id_role']);
    }
    
    public function getRolePrivate()
    {
       return $this->hasMany(RolePrivate::className(), ['id_role'=>'id_role']); 
    }
    
    
    public static function getList()
    {
       return ArrayHelper::map(static::find()->asArray()->all(), 'id_role', 'name'); 
    }
    
    
    


}
