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
use maze\table\ContentType;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class FieldExp extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%field_exp}}';
    }

    public function getTypeFields() {
        return $this->hasOne(Fields::className(), ['field_id' => 'field_id'])->from(["f" => Fields::tableName()]);
    }

    public function getWidgetName() {
        return FieldHelper::getInfoWidget($this->typeFields->type, $this->widget_name)->get('name');
    }

    public function getTypeName() {
        return FieldHelper::getInfoField($this->typeFields->type)->get('name');
    }
    
    public function getContentType(){
        return $this->hasOne(ContentType::className(), [
            'bundle' => 'bundle', 
            'expansion' => 'expansion'
            ])->from(["ct"=>ContentType::tableName()]);
    }

    public function getFieldObject() {

        if ($this->typeField) {
            $argu = $this->attributes;

            return FieldHelper::createField($this->typeFields->type, $argu);
        }
    }

    public function beforeSave($insert) {

        if (!empty($this->param)) {
            $this->param = serialize($this->param);
        }
        if (!empty($this->widget_param)) {
            $this->widget_param = serialize($this->widget_param);
        }
        return true;
    }

    public function afterFind() {
        if (!empty($this->param)) {
            $this->param = unserialize($this->param);
        }
        if (!empty($this->widget_param)) {
            $this->widget_param = unserialize($this->widget_param);
        }
    }

}
