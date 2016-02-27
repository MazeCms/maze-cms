<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\Expansion;
use maze\db\Expression;
use maze\table\Contents;
use maze\table\FieldExp;
use maze\table\DictionaryTerm;
use maze\helpers\ArrayHelper;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class ContentType extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%content_type}}';
    }
    public function rules() {
        return [
            [['title'], 'required'],
            [['expansion', 'bundle'], 'required', 'on'=>'create'], 
            ['bundle', 'match', 'pattern'=>'/^[a-z]{4,100}$/i', 'on'=>'create'],
            ['title', 'string', 'min'=>3, 'max'=>255],
            ['bundle','unique', 'targetClass'=>'maze\table\ContentType', 'targetAttribute'=>'bundle', 'filter'=>['expansion'=>$this->expansion], 'on'=>'create'],
            [['param', 'description'], 'safe']
        ];
    }

    public function getContents(){
         return $this->hasMany(Contents::className(), [
             'bundle' => 'bundle', 
             'expansion'=>'expansion'])->from(["cont"=>Contents::tableName()]);
    }
    
    public function getField(){
        return $this->hasMany(FieldExp::className(), [
             'bundle' => 'bundle', 
             'expansion'=>'expansion'])->from(["fe"=>FieldExp::tableName()]);
    }
    
    public function getTerm(){
        return $this->hasMany(DictionaryTerm::className(), [
             'bundle' => 'bundle', 
             'expansion'=>'expansion'])->from(["dt"=>DictionaryTerm::tableName()]);
    }
    
    public static function getList($condition = [])
    {
       return ArrayHelper::map(static::find()->andFilterWhere($condition)->asArray()->all(), 'bundle', 'title'); 
    }

    public function beforeSave($insert) {

        if (!empty($this->param)) {
            $this->param = serialize($this->param);
        }
        
        if($this->isNewRecord){             
            $this->date_create = new Expression('NOW()');
        }
        
        return true;
    }

    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }
    }

    public function attributeLabels() {
        return[
            'bundle'=>Text::_("LIB_FRAMEWORK_TABLE_CONTENTTYPE_BUNDLE"),
            'expansion'=>Text::_("LIB_FRAMEWORK_TABLE_CONTENTTYPE_EXPANSION"),
            'title'=>Text::_("LIB_FRAMEWORK_TABLE_CONTENTTYPE_TITLE"),
            'description'=>Text::_("LIB_FRAMEWORK_TABLE_CONTENTTYPE_DESCRIPTION")
        ];
    }

}
