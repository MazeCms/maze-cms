<?php

namespace exp\exp_contents\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\table\Contents;
use maze\table\FieldExp;
use maze\fields\FieldHelper;
use exp\exp_contents\form\FormType;

class ModelType extends Model {

    protected $type;
    
    protected $param;
    
    public $bundle;

    public function createType() {
        $this->type = new ContentType();
        $this->type->scenario = 'create';
        $this->type->expansion = 'contents';
        $this->param = RC::createObject(['class' => 'exp\exp_contents\form\FormType']);
        return $this;
    }

    public function getTypeByBundle($bundle) {
        $this->type = ContentType::findOne(['bundle' => $bundle, 'expansion' => 'contents']);
        if ($this->type) {
            $argum = $this->type->param;
            $argum['class'] = 'exp\exp_contents\form\FormType';
            $this->param = RC::createObject($argum);
            return $this;
        }
        return null;
    }

    public function getType() {
        return $this->type;
    }

    public function getParam() {
        return $this->param;
    }

    public function valid() {
        return Model::validateMultiple([$this->type, $this->param]);
    }

    public function loadAll($data) {
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
                throw new \Exception(Text::_("EXP_CONTENTS_MODELTYPE_ERRSAVE"));
            }
            if ($this->type->scenario == 'create') {
                $fieldTitle = FieldHelper::createField('title', [
                            'expansion' => 'contents',
                            'bundle' => trim($this->type->bundle),
                            'many_value' => 1,
                            'widget_name' => 'input',
                            'field_name' => 'title',
                            'active' => 1,
                            'title' => $this->param->title,
                            'param' => ['length' => $this->param->length]
                ]);
            } else {
                $fieldTitle = FieldHelper::find(['expansion' => 'contents', 'bundle' => $this->type->bundle]);
                $fieldTitle->title = $this->param->title;
                $fieldTitle->param = ['length' => $this->param->length];
                if (!$fieldTitle) {
                    throw new \Exception(Text::_("EXP_CONTENTS_MODELTYPE_ERRTITLE"));
                }
            }

            if ($fieldTitle && !$fieldTitle->save()) {
                throw new \Exception(Text::_("EXP_CONTENTS_MODELTYPE_ERRSAVETITLE"));
            }

            $transaction->commit();
            RC::getCache("fw_fields")->clearTypeFull();
            RC::getCache("exp_contents")->clearTypeFull();
        } catch (\Exception $ex) {
            $this->addError('type', $ex->getMessage());
            
            $transaction->rollBack();
            return false;
        }

        return true;
    }

    public function delete($bundle) {
        $type = ContentType::findOne(['bundle' => $bundle, 'expansion' => 'contents']);
        if (!$type) {
            $this->addError('bundle', Text::_('EXP_CONTENTS_TYPE_DELETE_NOTID', ['bundle' => $bundle]));
            return false;
        }
        if (Contents::find()->where(['bundle' => $bundle, 'expansion' => 'contents'])->exists()) {
            $this->addError('bundle', Text::_('EXP_CONTENTS_TYPE_DELETE_ISCONTENT', ['name' => $type->title]));
            return false;
        }

        $fieldExp = FieldHelper::findAll(['{{%field_exp}}.bundle' => $bundle, '{{%field_exp}}.expansion' => 'contents']);
        
        if (!$fieldExp) {
            $this->addError('bundle', 'EXP_CONTENTS_TYPE_DELETE_NOTFIELD');
            return false;
        }

        foreach ($fieldExp as $field) {
            if(!$field->delete()){
              $this->addError('bundle', 'EXP_CONTENTS_TYPE_DELETE_FIELD_ERR');
            }
        }
        $type->delete();

        RC::getCache("fw_fields")->clearTypeFull();
        RC::getCache("exp_contents")->clearTypeFull();
        return true;
    }

}
