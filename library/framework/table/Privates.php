<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\RolePrivate;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Privates extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%private}}';
    }
    
    public function getRolePrivate()
    {
        return $this->hasOne(RolePrivate::className(), ['id_priv'=>'id_priv']);
    }


}
