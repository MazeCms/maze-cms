<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\table\InstallApp;
use maze\table\AccessRole;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Plugin extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {
        return '{{%plugin}}';
    }
    
    public function rules() {
        return [
            [['ordering', 'enabled', 'param'], 'safe']
        ];
    }
    
    public function getInstallApp()
    {
        return $this->hasOne(InstallApp::className(), ['name' => 'name'])
                ->from(["ia"=>InstallApp::tableName()])
                ->andOnCondition('ia.type=:type', [':type'=>'plugin']);
    }
    
    public function beforeSave($insert) {

        if (!empty($this->param) && is_array($this->param)) {
            $this->param = serialize($this->param);
           
        }
        return true;
    }
    
    public function afterFind() {
        
        if (!empty($this->param) && is_string($this->param)) {
            $this->param = unserialize($this->param);
           
        }
    }
    
    public function getRole()
    {
        return $this->hasMany(Roles::className(), ['id_role' => 'id_role'])                
                ->viaTable(AccessRole::tableName(), ['key_id' => 'id_plg'], function($query){
                    $query->andOnCondition([AccessRole::tableName().'.exp_name'=>'plugins'])
                    ->andOnCondition([AccessRole::tableName().'.key_role'=>'plugin']);
                })->from(["r" => Roles::tableName()]);       
    }
    
    public function getAccessRole()
    {
        return $this->hasOne(AccessRole::className(), ['key_id' => 'id_plg'])
                ->from(["ar"=>AccessRole::tableName()])
                ->andOnCondition(['ar.exp_name'=>'plugins'])
                ->andOnCondition(['ar.key_role'=>'plugin']);
    }
    
}
