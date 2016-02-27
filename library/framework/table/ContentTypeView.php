<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace maze\table;

use maze\db\Expression;
use Text;
use maze\table\FieldExp;
/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class ContentTypeView extends \maze\db\ActiveRecord {

    public static function tableName() {
        return '{{%content_type_view}}';
    }
    
    public function rules() {
        return [
            [['bundle', 'expansion', 'field_exp_id', 'mode', 'field_view', 'entry_type', 'view_name', 'group_name'], 'required'],           
            ['bundle', 'match', 'pattern'=>'/^[a-z]{3,100}$/i'],
            ['view_name', 'match', 'pattern'=>'/^[a-z]{3,60}$/i'],
            ['group_name', 'match', 'pattern'=>'/^[a-z]{3,60}$/i'],
            ['field_view', 'match', 'pattern'=>'/^[a-z]{3,50}$/i'],
            ['view_name', 'unique', 'on'=>'create'],
            ['class_wrapper', 'string', 'max'=>255],
            ['tag_wrapper', 'string', 'max'=>10],
            [['multiple_size', 'multiple_start'], 'number', 'min'=>0],
            [['mode', 'enabled', 'show_label'], 'boolean'],
            [['field_view_param'], 'safe']
        ];
    }
    
    public function getFieldExp(){
        return $this->hasOne(FieldExp::className(), [
            'field_exp_id' => 'field_exp_id',
            ])->from(["fe"=>FieldExp::tableName()]);
    }
    
    public function beforeSave($insert) {
        if (!empty($this->field_view_param)) {
            $this->field_view_param = serialize($this->field_view_param);
        }   
        return true;
    }

    public function afterFind() {
        if (!empty($this->field_view_param)) {
            $this->field_view_param = unserialize($this->field_view_param);
        }
    }

    public function attributeLabels() {
        return[
            'bundle'=>Text::_('LIB_FRAMEWORK_TABLE_CONTENTS_BUNDLE'),
            'expansion'=>Text::_('LIB_FRAMEWORK_TABLE_CONTENTS_EXPANSION'),
            'field_exp_id'=>Text::_('ID поля'),
            'mode'=>Text::_('Режим отображения'),
            'field_view'=>Text::_('Вид поля'),
            'enabled'=>Text::_('Активность'),
            'show_label'=>Text::_('Показывать заголовок поля'),
            'group_name'=>Text::_('Группа поля'),
            'class_wrapper'=>Text::_('CSS класс обертки поля'),
            'tag_wrapper'=>Text::_(' HTML тег обертки поля'),
            'multiple_size'=>Text::_('Выводить количество элементов'),
            'multiple_start'=>Text::_('Начинать вывод с элемента'),
            'view_name'=>Text::_('Код вида поля'),
        ];
    }

}
