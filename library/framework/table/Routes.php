<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\db\Expression;
use maze\table\Fields;
use maze\fields\FieldHelper;
use Text;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Routes extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%routes}}';
    }

     public function rules() {
        return [
            [['alias', 'expansion'], 'required'],
            [['alias'], 'validAlias'],
            [['meta_title', 'alias'], 'string', 'min'=>1, 'max'=>255],
            ['meta_keywords', 'string', 'min'=>1, 'max'=>500],
            ['meta_robots', 'string', 'min'=>1, 'max'=>45],
            ['meta_description', 'string'],
            [['expansion'], 'string', 'min'=>3, 'max'=>255]
        ];
    }
    
    public function validAlias($attribute, $param){
        $q = self::find()->where(['alias'=>$this->alias]);
        if($this->routes_id){
            $q->andWhere(['!=', 'routes_id', $this->routes_id]);
        }
        if($q->exists()){
            $this->addError($attribute, Text::_("LIB_FRAMEWORK_TABLE_ROUTES_VALIDALIAS"));
        }
    }

        public function beforeSave($insert) {
        if($this->isNewRecord){             
            $this->date_create = new Expression('NOW()');
        }  
        return true;
    }

    public function attributeLabels() {
        return[
            'alias'=>Text::_('LIB_FRAMEWORK_TABLE_ROUTES_ALIAS'),
            'meta_title'=>Text::_('LIB_FRAMEWORK_TABLE_ROUTES_METATITLE'),
            'meta_keywords'=>Text::_('LIB_FRAMEWORK_TABLE_ROUTES_METAKEYWORDS'),
            'meta_description'=>Text::_('LIB_FRAMEWORK_TABLE_ROUTES_METADESCRIPTION'),
            'meta_robots'=>Text::_('LIB_FRAMEWORK_TABLE_ROUTES_METAROBOTS')
        ];
    }

}
