<?php

/**
 * BaseDataField - Базовый класс данных поля
 *
 * @author nick
 */

namespace maze\fields;

use RC;
use maze\base\Model;
use maze\db\Query;
use maze\fields\FieldHelper;
use Text;

class BaseDataField extends Model {

    public $id;
    public $field_exp_id;
    public $entry_id;
    public $id_lang;

    public function formName() {
        return $this->getField()->field_name;
    }

    public function rules() {
        $result = $this->fieldRule();

        $result[] = [['field_exp_id'], 'required'];
        $result[] = ['id_lang', 'default', 'value'=>0];
        $result[] = ['field_exp_id', 'validExistsField'];
        $result[] = ['field_exp_id', 'validManyValue'];
        return $result;
    }

    public function validExistsField($attr, $param) {
        $result = (new Query())
                ->from('{{%field_exp}}')
                ->where(['field_exp_id' => $this->field_exp_id])
                ->exists();

        if (!$result) {
            $this->addError($attr, 'Поля с ID(' . $this->field_exp_id . ')  не существует');
        }
    }
    
    public function validManyValue($attr, $param){
        $field = $this->getField();
        $result = count($field->data);
        if ($field->many_value != 0 && $result > $field->many_value) {
            $this->addError($attr, Text::_('Превышено максимальное {count} допустимное количество значений для {name} поля', ['name'=>$field->title, 'count'=>$result]));
        }
    }

    public function fieldRule() {
        return [];
    }

    public function getField() {
        return FieldHelper::findByID($this->field_exp_id);
    }

    public function save() {

        $field = $this->getField();
        $this->field_exp_id = $field->field_exp_id;
        
        if ($this->validate()) {
             if(!$this->beforeSave()){
                return true;
            }
            $data = $this->attributes;
            $db = RC::getDb();
            $db->createCommand()->insert($field->getTableName(), $data)->execute();
            $table = $db->getTableSchema($field->getTableName());
            $this->id = $db->getLastInsertID($table->sequenceName);
        }
        return $this->id;
    }
    
    public function beforeSave() {
        return true;
    }

    /**
     * @return boolean - проверка на новое поле
     */
    public function getIsNew() {
        return $this->id == null;
    }

}
