<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\Expansion;
use maze\table\AccessRole;
use maze\table\query\MenuQuery;
use maze\table\MenuGroup;
use maze\table\Roles;
use maze\table\Languages;
use maze\table\Routes;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Menu extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%menu}}';
    }
    public function rules() {
        return [
            [
            ['id_group', 'typeLink', 'name', 'image',  'paramLink',
                'time_active', 'time_inactive', 'ordering', 'enabled', 
                'id_tmp','id_lang', 'id_exp', 'parent', 'home', 'param'], 'safe']
        ];
    }

    public static function find() {
        return new MenuQuery(get_called_class());
    }

    public function getExpansion() {
        return $this->hasOne(Expansion::className(), ['id_exp' => 'id_exp'])->from(["exp" => Expansion::tableName()]);
    }

    public function getGroup() {
        return $this->hasOne(MenuGroup::className(), ['id_group' => 'id_group']);
    }
    
    public function getLang(){
        return $this->hasOne(Languages::className(), ['id_lang' =>'id_lang']);
    }

    public function getAccessRole() {
        return $this->hasMany(AccessRole::className(), ['key_id' => 'id_menu'])
                ->from(["ar" => AccessRole::tableName()])
                ->andOnCondition(['ar.exp_name'=>'menu'])
                ->andOnCondition(['ar.key_role'=>'items']);
    }
    
    public function getRoute(){
        return $this->hasOne(Routes::className(), [
            'routes_id' => 'routes_id', 
            ])->from(["route"=>Routes::tableName()]);
    }
    
    public function getRole()
    {
        return $this->hasMany(Roles::className(), ['id_role' => 'id_role'])                
                ->viaTable(AccessRole::tableName(), ['key_id' => 'id_menu'], function($query){
                    $query->andOnCondition([AccessRole::tableName().'.exp_name'=>'menu'])
                    ->andOnCondition([AccessRole::tableName().'.key_role'=>'items']);
                })->from(["r" => Roles::tableName()]);       
    }

    public function getCountChild() {
        return $this->hasMany(Menu::className(), ['parent' => 'id_menu'])->count();
    }

    public function beforeSave($insert) {

        if (!empty($this->param)) {
            $this->param = serialize($this->param);
        }

        if (!empty($this->paramLink) && $this->typeLink == 'expansion') {
            $this->paramLink = serialize($this->paramLink);
        }
        return true;
    }
    
    public function afterSave($param) {
        \RC::getPlugin("menu")->triggerHandler("afterARSaveItem", [$this->id_menu, $this->isNewRecord, $this]);
    }

    public function afterDelete() {
       \RC::getPlugin("menu")->triggerHandler("afterARDeleteItem", [$this->id_menu]);
    }
    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }

        if (!empty($this->paramLink) && $this->typeLink == 'expansion') {
            $this->paramLink = unserialize($this->paramLink);
        }
    }

    public function attributeLabels() {
        return[
            "id_menu" => 'ID',
            "id_group" => "Меню",
            "typeLink" => "Тип ссылки",
            "name" => "Название",
            "ordering" => "Сортировка",
            "alias" => "Алис",
            "meta_key" => "Ключевые слова",
            "meta_des" => "Мета описание",
            "meta_data" => "Мета данные",
            "image" => "Иконка",
            "paramLink" => "Параметры ссылке",
            "enabled" => "Активность",
            "home" => "Главная"
        ];
    }

}
