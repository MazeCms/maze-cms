<?php

namespace exp\exp_dictionary\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\DictionaryTerm;
use maze\table\Routes;
use exp\exp_dictionary\form\FormType;
use exp\exp_dictionary\model\ModelType;
use maze\table\AccessRole;

class ModelTerm extends Model {

    public $id;
    
    public $bundle;

    protected $type;
    
    protected $fields;
    
    protected $routes;
    
    protected $term;
    
    public $id_role;

    public static $_parents;
    
    public function rules() {
        return [
            [['id_role'], 'safe']
        ];
    }

    public function getType() {
        if ($this->bundle && $this->type == null) {
            $this->type = (new ModelType())->getTypeByBundle($this->bundle);
        }

        return $this->type;
    }
    
    

    public function getTerm() {
        if ($this->term == null) {
            if ($this->id) {
                $this->term = DictionaryTerm::find()                        
                        ->from(['dt'=>DictionaryTerm::tableName()])
                        ->joinWith(['route', 'accessRole'=>function($qu){
                           $qu->andOnCondition(['ar.exp_name'=>'dictionary'])
                                ->andOnCondition(['ar.key_role'=>'term']);
                        }])
                        ->where(['dt.expansion' => 'dictionary', 'dt.term_id' => $this->id])
                        ->one();
            } else {
                $this->term = new DictionaryTerm();
                $this->term->expansion = 'dictionary';
                $this->term->bundle = $this->bundle;
                $this->term->enabled = $this->getType()->param->enabled;
            }
        }
        return $this->term;
    }
    
    public function enabled($id, $active) {
        return DictionaryTerm::updateAll(['enabled'=>$active], ['expansion' => 'dictionary', 'term_id' =>$id]);           
    }

    public function getRoutes() {
        if ($this->routes == null) {
            if ($this->id) {
                if ($cont = $this->getTerm()) {
                    $this->routes = $cont->route;
                }
            } else {
                $this->routes = new Routes();
            }
            $this->routes->expansion = 'dictionary';
        }
        return $this->routes;
    }
    
    public function find($id){
        $this->id = $id;
        $term = $this->getTerm();
        
        if(!$term) return false;
        $this->bundle = $term->bundle;
        
        $fields = $this->getFields();
        
        foreach($fields as $field){
           if(!$field->findData(['entry_id'=>$term->term_id])){
               $field->addData();
           }
        }
        
        if($role = $term->accessRole){
            $this->id_role = array_map(function($val){return $val->id_role;}, $role);
        }
        
        return true;
    }

    public function getFields() {
        if ($this->bundle && $this->fields == null) {
            $this->fields = FieldHelper::findAll(['expansion' => 'dictionary', 'bundle' => $this->bundle, 'active'=>1]);
        }
        return $this->fields;
    }
    
    public function getParents($bundle){
        if(!isset(static::$_parents[$bundle])){
            $terms = DictionaryTerm::find()
                        ->where(['expansion' => 'dictionary', 'bundle' => $bundle])
                        ->all(); 
            static::$_parents[$bundle] = [];
        
            foreach($terms as $t){
                static::$_parents[$bundle][$t->parent][] = $t;
            }
        }
        
        return static::$_parents[$bundle];
    }


    public function getChildrenTerm($id, $bundle){
        $result = [];        
        $parent = $this->getParents($bundle);
        
        if(isset($parent[$id])){
            $target = $parent[$id];
            foreach($target as $p){
               $result[] = $p;
               $result = array_merge($result, $this->getChildrenTerm($p->term_id, $bundle));
            }
        }
        return $result;
    }
    
    public function getChildrenTermID($id, $bundle){
        $result = $this->getChildrenTerm($id, $bundle);
        
        return array_map(function($val){return $val->term_id;}, $result);
    }
    
    public function getTermDisabled($id, $bundle){
        $result = $this->getChildrenTermID($id, $bundle);
        $result[] = $id;
        return $result;
    }

    public function getField($name) {
        $result = null;
        foreach ($this->getFields() as $field) {
            if ($field->field_name == $name) {
                $result = $field;
                break;
            }
        }
        return $result;
    }
    
    public function getTitle(){
      $field =  $this->getField('title');
      if($field){
          if($field->data){
              return $field->data[0]->title_value;
          }
      }
    }
    
    public function resetField(){
        foreach ($this->getFields() as $field) {
            $field->resetData();
        }
    }

    public function loadAll($data) {

        
        $this->getTerm()->load($data);
        if (!$this->getType()->param->multilang) {
            $this->term->id_lang = 0;
        }

        $this->getRoutes()->load($data);
        $this->resetField();
        if(isset($data['ModelTerm']['id_role'])){
           $this->id_role = $data['ModelTerm']['id_role'];
        }else{
           $this->id_role = null;
        }
       
        foreach ($this->getFields() as $field) {
            if (isset($data[$field->field_name])) {
                $fieldT = $data[$field->field_name];
                if (ArrayHelper::isIndexed($fieldT)) {
                    foreach ($fieldT as $f) {
                        $f['id_lang'] = $this->term->id_lang;
                        $field->addData($f);
                    }
                } else {
                    $fieldT['id_lang'] = $this->term->id_lang;
                    $field->addData($fieldT);
                }
            } else {
                $field->addData();
            }
        }
    }

    public function getAllModel() {
        $models = [];

        $models[] = $this->getTerm();
        $models[] = $this->getRoutes();

        foreach ($this->getFields() as $field) {
            $models = array_merge($models, $field->data);
        }
        return $models;
    }

    /**
     * сохранить материал
     * 
     * return boolean
     */
    public function save() {
        $transaction = RC::getDb()->beginTransaction();
        try {
            if (!$this->routes->validate() || !$this->routes->save()) {
                throw new \Exception("EXP_DICTIONARY_ROUTEBD_SAVE_ERR");
            }
            if ($this->term->isNewRecord) {
                
                $this->term->routes_id = $this->routes->routes_id;
                $this->term->sort = DictionaryTerm::find()->where(['expansion' => 'dictionary', 'bundle' => $this->bundle])->count() + 1;
            }

           
            if (!$this->term->validate() || !$this->term->save()) {
                throw new \Exception("EXP_DICTIONARY_CONTENTBD_SAVE_ERR");
            }
            
            AccessRole::deleteAll(['exp_name' => 'dictionary', 'key_role' => 'term', 'key_id' =>$this->term->term_id]);
            
            if (!empty($this->id_role) && is_array($this->id_role)) {
                foreach ($this->id_role as $id_role) {
                    $role = new AccessRole();
                    $role->exp_name = 'dictionary';
                    $role->key_role = 'term';
                    $role->key_id = $this->term->term_id;
                    $role->id_role = $id_role;
                    if (!$role->save()) {
                        $this->addError('id_role', 'Ошибка сохранения роли пункта  меню');
                        throw new \Exception();
                    }
                }
            }
            
            foreach ($this->getFields() as $field) {
                $field->deleteData(['entry_id' => $this->term->term_id]);
                foreach ($field->data as $data) {
                    $data->id_lang = $this->term->id_lang;
                    $data->entry_id = $this->term->term_id;
                    if (!$data->validate() || !$data->save()) {
                        throw new \Exception("EXP_DICTIONARY_FIELD_SAVE_ERR");
                    }
                }
            }
            
            $this->id = $this->term->term_id;
            $transaction->commit();
            RC::getCache("fw_fields")->clearTypeFull();
            RC::getCache("exp_dictionary")->clearTypeFull();
        } catch (\Exception $ex) {
            $this->addError('bundle', $ex->getMessage());
            $transaction->rollBack();
            return false;
        }

        return true;
    }
    
    public function delete(){
        $transaction = RC::getDb()->beginTransaction();
        
         try {
             if (!$this->id) {
                 throw new \Exception("ID не может быть пустым");
             }
             
             if(!$this->find($this->id)){
                    throw new \Exception(Text::_('По текущего ID:{id} ничего не найдено', ['id'=>$this->id]));
             }
             
            foreach ($this->getFields() as $field) {
                $field->deleteData(['entry_id' => $this->term->term_id]);
            }
            
            $this->getRoutes()->delete();
            $this->getTerm()->delete();
             
          $transaction->commit();
          RC::getCache("fw_fields")->clearTypeFull();
          RC::getCache("exp_dictionary")->clearTypeFull();
        } catch (\Exception $ex) {
            $this->addError('bundle', $ex->getMessage());
            $transaction->rollBack();
            return false;
        }

        return true;
    }
    
    public function deleteAll(){
        $transaction = RC::getDb()->beginTransaction();
        
         try {
             if (!$this->id) {
                 throw new \Exception("ID не может быть пустым");
             }
            if(!$this->find($this->id)){
                    throw new \Exception(Text::_('По текущего ID:{id} ничего не найдено', ['id'=>$this->id]));
            }
            
            $ids = $this->getChildrenTermID($this->id, $this->bundle);
            foreach($ids as $id){
                if(!(new self(['id'=>$id]))->delete()){
                    throw new \Exception(Text::_("Ошибка удаления дочернего термина с ID:{id}", ['id'=>$id]));
                }
            }
            $this->delete();             
            $transaction->commit();
            RC::getCache("fw_fields")->clearTypeFull();
            RC::getCache("exp_dictionary")->clearTypeFull();
        } catch (\Exception $ex) {
            $this->addError('bundle', $ex->getMessage());
            $transaction->rollBack();
            return false;
        }

        return true;
    }
    
    public function attributeLabels() {
        return[
            'id_role'=>Text::_('EXP_DICTIONARY_ACCESS_ROLE'),
        ];
    }

}
