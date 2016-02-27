<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\db\ActiveRecord;
use maze\table\WidgetsMenu;
use maze\table\WidgetsExp;
use maze\table\AccessRole;
use maze\table\InstallApp;
use maze\table\Roles;
use maze\table\Languages;
use maze\table\Template;
use maze\table\WidgetsUrl;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Widgets extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%widgets}}';
    }
    
    public function rules() {
        return [
            [
            ['name', 'title', 'position', 'ordering', 'time_cache',  
                'enable_cache', 'time_active', 'time_inactive', 'enabled', 
                'enable_php','php_code', 'title_show', 'id_tmp', 'id_lang', 'param'], 'safe']
        ];
    }
    
    public function getMenu()
    {
        return $this->hasMany(WidgetsMenu::className(), ['id_wid' => 'id_wid'])->from(["menu"=>WidgetsMenu::tableName()]);
    }
    public function getApp()
    {
        return $this->hasOne(InstallApp::className(), ['name' => 'name'])->from(["app"=>InstallApp::tableName()])
                ->andWhere(['app.type'=>'widget']);
    }
    public function getExp()
    {
        return $this->hasMany(WidgetsExp::className(), ['id_wid' => 'id_wid'])->from(["exp"=>WidgetsExp::tableName()]);
    }
    public function getUrl()
    {
        return $this->hasMany(WidgetsUrl::className(), ['id_wid' => 'id_wid'])->from(["url"=>WidgetsUrl::tableName()])->addOrderBy('url.sort');
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
    
    public function getAccessRole() {
        return $this->hasMany(AccessRole::className(), ['key_id' => 'id_wid'])
                ->from(["ar" => AccessRole::tableName()])
                ->andOnCondition(['ar.exp_name'=>'widget'])
                ->andOnCondition(['ar.key_role'=>'widget']);
    }
    
    public function getRole()
    {
        return $this->hasMany(Roles::className(), ['id_role' => 'id_role'])                
                ->viaTable(AccessRole::tableName(), ['key_id' => 'id_wid'], function($query){
                    $query->andOnCondition([AccessRole::tableName().'.exp_name'=>'widget'])
                    ->andOnCondition([AccessRole::tableName().'.key_role'=>'widget']);
                })->from(["r" => Roles::tableName()]);       
    }
    
    
    public function getLang(){
        return $this->hasOne(Languages::className(), ['id_lang' =>'id_lang']);
    }
    
    public function getTmp(){
        return $this->hasOne(Template::className(), ['id_tmp' =>'id_tmp']);
    }

    
}
