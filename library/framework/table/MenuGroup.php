<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace maze\table;
 use maze\table\Menu;
 use maze\helpers\ArrayHelper;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class MenuGroup extends \maze\db\ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%menu_group}}';
    }
    
    public function rules() {
        return [
            [['name', 'description', 'ordering', 'code'], 'safe']];
        
    }
    
    public function getItems()
    {
        return $this->hasMany(Menu::className(), ['id_group' => 'id_group']);
    }
    
    public function getCountItems()
    {
        return $this->hasMany(Menu::className(), ['id_group' => 'id_group'])->count();
    }
    public static function getList()
    {
       return ArrayHelper::map(static::find()->asArray()->all(), 'id_group', 'name'); 
    }
    
    public function attributeLabels() {
        return[
            "id_group" =>'ID',
            "name"=>"Меню",
            "safe"=>"Код меню",
            "description" => "Описание",
            "ordering" => "Сортировка",
        ];
    }
   
}
