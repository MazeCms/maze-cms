<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\table\Expansion;
use maze\db\Expression;
use maze\table\ContentType;
use maze\table\FieldExp;
use maze\table\Routes;
use maze\table\Languages;
use maze\table\AccessRole;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class DictionaryTerm extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%dictionary_term}}';
    }
    public function rules() {
        return [
            [['bundle', 'expansion'], 'required'],            
            ['bundle', 'match', 'pattern'=>'/^[a-z]{3,100}$/i'],
            [['parent', 'id_lang'], 'default', 'value'=>0],
            [['routes_id', 'id_lang','sort', 'parent'], 'number'],
            [['enabled'], 'boolean'],
            [['time_active', 'time_inactive'], 'date', 'format'=>'Y-m-d H:i:s'],
            [['expansion'], 'string', 'min'=>3, 'max'=>255]
        ];
    }

    public function getType(){
        return $this->hasOne(ContentType::className(), [
            'bundle' => 'bundle', 
            'expansion' => 'expansion'
            ])->from(["type"=>ContentType::tableName()]);
    }
    
    public function getFields(){
        return $this->hasMany(FieldExp::className(), [
            'bundle' => 'bundle', 
            'expansion' => 'expansion'
            ])->from(["fe"=>FieldExp::tableName()]);
    }
    
    public function getRoute(){
        return $this->hasOne(Routes::className(), [
            'routes_id' => 'routes_id', 
            ])->from(["route"=>Routes::tableName()]);
    }

    public function getLang(){
        return $this->hasOne(Languages::className(), ['id_lang' =>'id_lang'])->from(["l"=>Languages::tableName()]);
    }

    public function getAccessRole() {
        return $this->hasMany(AccessRole::className(), ['key_id' => 'term_id'])
                ->from(["ar" => AccessRole::tableName()]);
                
    }
    
    public function beforeSave($insert) {

        if($this->isNewRecord){             
            $this->date_create = new Expression('NOW()');
        }
        
        return true;
    }

    public function attributeLabels() {
        return[
            'bundle'=>Text::_('LIB_FRAMEWORK_TABLE_CONTENTS_BUNDLE'),
            'expansion'=>'LIB_FRAMEWORK_TABLE_CONTENTS_EXPANSION',
            'routes_id'=>'LIB_FRAMEWORK_TABLE_CONTENTS_ROUTESID',
            'enabled'=>Text::_('LIB_FRAMEWORK_TABLE_CONTENTS_ENABLED'),
            'id_lang'=>Text::_('LIB_FRAMEWORK_TABLE_CONTENTS_LANG'),
            'parent'=>Text::_('Родитеский термин'),
            'time_active'=>Text::_('Дата начала публикации'),
            'time_inactive'=>Text::_('Дата окончания публикации')
        ];
    }

}
