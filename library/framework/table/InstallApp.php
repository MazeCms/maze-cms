<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;

use maze\table\Expansion;
use maze\table\GroupExp;
use maze\db\Expression;
use maze\table\Plugin;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class InstallApp extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {
        return '{{%install_app}}';
    }
    
    public function getExpansion() {
        return $this->hasOne(Expansion::className(), ['name' => 'name'])
                        ->from(["exp" => Expansion::tableName()]);
    }

    public function getGroupExp() {
        return $this->hasOne(GroupExp::className(), ['name' => 'group_name'])
                        ->from(["ge" => GroupExp::tableName()]);
    }
    
    public function getPlugin(){
         return $this->hasOne(Plugin::className(), ['name' => 'name'])
                        ->from(["plg" => Plugin::tableName()]);
    }
    
    public function beforeSave($insert) {

        if ($this->isNewRecord) {
            $this->install_data = new Expression('NOW()');
           
        }
        return true;
    }
    
    public static function getListExp($front = 1)
    {
        $query = InstallApp::find()->innerJoinWith(['expansion'])->where([static::tableName().'.front_back'=>$front, static::tableName().'.type'=>'expansion'])->all();
     
        $result = [];
        
        foreach($query as $exp)
        {
            $info = \RC::getConf(["type"=>"expansion", "name"=>$exp->name]);
            $app_name = $info->get("name") ? $info->get("name") : $exp->name;
			unset($info);					
            $result[$exp->expansion->id_exp] = $app_name;
             
        }
        
        return $result;
    }
}
