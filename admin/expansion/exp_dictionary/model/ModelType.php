<?php

namespace exp\exp_dictionary\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\table\DictionaryTerm;
use maze\table\FieldExp;
use maze\fields\FieldHelper;
use exp\exp_contents\form\FormType;

class ModelType extends Model {
    
    protected $type;
    
    protected $param;
    
    public $bundle;


    public function createType(){
       $this->type = new ContentType();
       $this->type->scenario = 'create';
       $this->type->expansion = 'dictionary';
       $this->param = RC::createObject(['class'=>'exp\exp_dictionary\form\FormType']);
       return $this;
    }
    
    public function getTypeByBundle($bundle){
       $this->type = ContentType::findOne(['bundle'=>$bundle, 'expansion'=>'dictionary']);
       if($this->type){
           $argum = $this->type->param;
           $argum['class'] ='exp\exp_dictionary\form\FormType'; 
           $this->param = RC::createObject($argum);           
           return $this;
       }
       return null;
    }
    
   
    public function getType(){
        return $this->type;
    }

    public function getParam(){
        return  $this->param;
    }
    public function valid(){
        return Model::validateMultiple([$this->type, $this->param]);
    }
    
    public function loadAll($data){
        $this->type->load($data);
        $this->param->load($data);
    }
    
    /**
     * сохранить тип материалов
     * 
     * @param ContentType $table
     * @param Model $param - 
     */
    public function save() {
        $transaction = RC::getDb()->beginTransaction();
        try {
            $this->type->param = $this->param->attributes;
            if (!$this->type->save()) {
                throw new \Exception(Text::_("EXP_DICTIONARY_MODELTYPE_ERRSAVE"));
            }
            if ($this->type->scenario == 'create') {
               $fieldTitle = FieldHelper::createField('title', [
                    'expansion' => 'dictionary',
                    'bundle' => $this->type->bundle,
                    'many_value' => 1,
                    'widget_name' => 'input',
                    'field_name' => 'title',
                    'active'=>1,
                    'title' => $this->param->title,
                    'param'=>['length'=>$this->param->length]
                ]);
            } else {
               $fieldTitle = FieldHelper::find(['expansion'=>'dictionary', 'bundle' => $this->type->bundle]);
               $fieldTitle->title = $this->param->title;
               $fieldTitle->param = ['length'=>$this->param->length];
               if(!$fieldTitle){
                   throw new \Exception(Text::_("EXP_DICTIONARY_MODELTYPE_ERRTITLE"));
               }
            }
            
            if(!$fieldTitle->save()){               
               throw new \Exception(Text::_("EXP_DICTIONARY_MODELTYPE_ERRSAVETITLE"));
            }

            $transaction->commit();
            RC::getCache("fw_fields")->clearTypeFull();
            RC::getCache("exp_dictionary")->clearTypeFull();
        } catch (\Exception $ex) {
            $this->addError('type', $ex->getMessage());
            $transaction->rollBack();
            return false;
        }
        
        return true;
    }
    
    public function delete($bundle) {
        $type = ContentType::findOne(['bundle' => $bundle, 'expansion' => 'dictionary']);
        if (!$type) {
            $this->addError('bundle', Text::_('EXP_DICTIONARY_TYPE_DELETE_NOTID', ['bundle' => $bundle]));
            return false;
        }
        if (DictionaryTerm::find()->where(['bundle' => $bundle, 'expansion' => 'dictionary'])->exists()) {
            $this->addError('bundle', Text::_('EXP_DICTIONARY_TYPE_DELETE_ISCONTENT', ['name' => $type->title]));
            return false;
        }

        $fieldExp = FieldHelper::findAll(['{{%field_exp}}.bundle' => $bundle, '{{%field_exp}}.expansion' => 'dictionary']);
        
        if (!$fieldExp) {
            $this->addError('bundle', 'EXP_DICTIONARY_TYPE_DELETE_NOTFIELD');
            return false;
        }

        foreach ($fieldExp as $field) {
            if(!$field->delete()){
              $this->addError('bundle', 'EXP_DICTIONARY_TYPE_DELETE_FIELD_ERR');
            }
        }
        // удалем все связанные поля с данным словарем
        $fieldExpBind = FieldExp::find()->all(); 
        foreach ($fieldExpBind as $field) {
            if($field->param && isset($field->param['dictionary']) && $field->param['dictionary'] == $bundle){
                if($fieldObj = FieldHelper::findByID($field->field_exp_id)){
                    if(!$fieldObj->delete()){
                       $this->addError('bundle', 'EXP_DICTIONARY_TYPE_DELETE_FIELD_ERR');
                    } 
                }   
            }
        }
        $type->delete();

        RC::getCache("fw_fields")->clearTypeFull();
        RC::getCache("exp_dictionary")->clearTypeFull();
        return true;
    }

}
