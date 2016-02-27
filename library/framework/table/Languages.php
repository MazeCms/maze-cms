<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;
use maze\table\LangApp;
use maze\helpers\ArrayHelper;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Languages extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%languages}}';
    }
    
    public function rules() {
        return [
            [['lang_code', 'title', 'reduce', 'img'], "required"],
            [['title', 'img'], 'string', 'max' => 100],
            ['lang_code', 'match', 'pattern'=>'/^[a-z]{2,4}-[A-Z]{2,4}$/i'],
            ['reduce', 'match', 'pattern'=>'/^[a-z]{2,4}$/i'],
            ['enabled','boolean'],
            ['enabled', 'default', 'value'=>0],
            ['lang_code', 'unique', 'targetClass'=>'maze\table\Languages', 'targetAttribute'=>'lang_code', 'filter'=>function($query){
                if($this->id_lang) $query->andFilterWhere(['not', ['id_lang'=>$this->id_lang]]);
            }],            
            [['ordering'], 'number'],
        ];
    }
    
    
    public function getApp()
    {
        return $this->hasMany(LangApp::className(), ['id_lang'=>'id_lang'])
                ->from(["langapp"=>LangApp::tableName()]);
    }
    
    public static function getList()
    {
       return ArrayHelper::map(static::find()->orderBy('ordering')->asArray()->all(), 'id_lang', 'title'); 
    }

    
    public function attributeLabels() {
        return[
            "lang_code" => Text::_("LIB_USERINTERFACE_FIELD_LANG_LANGCODE"),
            "title" => Text::_("LIB_USERINTERFACE_FIELD_TITLE"),
            "reduce" => Text::_("LIB_USERINTERFACE_FIELD_LANG_REDUCE"),
            "ordering" => Text::_("LIB_USERINTERFACE_FIELD_SORT"),
            "img" => Text::_("LIB_USERINTERFACE_FIELD_LANG_ICON"),
            "enabled" => Text::_("LIB_USERINTERFACE_FIELD_ENABLED"),
        ];
    }

}
