<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;
use maze\helpers\ArrayHelper;
use maze\table\Expansion;
use maze\table\Menu;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Template extends \maze\db\ActiveRecord {
   
    public function rules() {
        return [
            [['name', 'title', 'home', 'front', 'time_active', 'time_inactive', 'param'], 'safe']
        ];
    }
    public static function tableName()
    {       
        return '{{%template}}';
    }
    
    public function admin()
    {
        $this->andWhere(['front' => 0]);
        return $this;
    }
    
    public function site()
    {
        $this->andWhere(['front' => 1]);
        return $this;
    }
    
    public function getExpansion() {
        return $this->hasMany(Expansion::className(), ['id_tmp' => 'id_tmp'])
                ->from(["exp" => Expansion::tableName()]);
    }
    
    public function getMenu() {
        return $this->hasMany(Menu::className(), ['id_tmp' => 'id_tmp'])
                ->from(["m" => Menu::tableName()]);
    }
    
    public static function getList()
    {
        return ArrayHelper::map(static::find()->where(['front' => 1])->asArray()->all(), 'id_tmp', 'title'); 
    }
    
    public function beforeSave($insert) {

        if (!empty($this->param)) {
            $this->param = serialize($this->param);
        }

        return true;
    }

    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }
    }
    
    
}
