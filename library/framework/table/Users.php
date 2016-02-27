<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\UserMeta;
use maze\table\Languages;
use maze\table\Roles;
use maze\table\UserRoles;
use maze\db\Expression;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Users extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%users}}';
    }    
      
    public function getLanguages()
    {
        return $this->hasMany(Languages::className(), ['id_lang'=>'id_lang'])->from(['lang'=>Languages::tableName()]);
    }
    
    public function getLang()
    {
        return $this->hasOne(Languages::className(), ['id_lang'=>'id_lang'])->from(['lang'=>Languages::tableName()]);
    }
    
    public function getRole()
    {
         return $this->hasMany(Roles::className(), ['id_role' => 'id_role'])                
                ->viaTable(UserRoles::tableName(), ['id_user' => 'id_user'])->from(["r" => Roles::tableName()]);     
    }
    /**
     * Safe Attributes
     * return [
     *    'login' => ['username', 'password'],
     *    'register' => ['username', 'email', 'password'],
     * ];
     * @return type
     */
    public function scenarios() {
        return [
            'access' => ['lastvisitDate', 'timeactiv', 'status'],
            'save'=>['username', 'name', 'avatar', 'email', 'password', 'id_lang', 'timezone', 'editor_admin', 'editor_site', 'bloc'],
            'recover'=>['keyactiv', 'timeactiv'],
            'editpass'=>['keyactiv', 'password']
        ];
    }
    
    public function beforeSave($insert) {

        if($this->isNewRecord)
        {
            $this->registerDate = new Expression("NOW()");
        }
        return true;
    }

}
