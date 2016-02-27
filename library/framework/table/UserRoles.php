<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;
use maze\table\AccessDynamic;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class UserRoles extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%user_roles}}';
    }
    
    public function rules() {
        return [
            [["id_role", "id_user"], "required"],
            ['id_role', 'exist', 'targetClass'=>'maze\table\Roles', 'targetAttribute'=>'id_role', 'on'=>'create']
        ];
    }
    
    public function getAccessDynamic()
    {
        return $this->hasMany(AccessDynamic::className(), ['id_role'=>'id_role']);
    }
    


}
