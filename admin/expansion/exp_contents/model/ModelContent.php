<?php

namespace exp\exp_contents\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use exp\exp_contents\form\FormType;
use exp\exp_contents\model\ModelType;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\Contents;
use maze\table\Routes;
use maze\table\ContentsHome;
use maze\table\AccessRole;
use maze\table\ContentTermSort;

class ModelContent extends Model {

    public $id;
    public $bundle;
    protected $type;
    protected $fields;
    protected $routes;
    protected $contents;
    public $id_role;

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

    public function getContents() {
        if ($this->contents == null) {
            if ($this->id) {
                $this->contents = Contents::find()
                        ->from(['c' => Contents::tableName()])
                        ->joinWith(['route', 'accessRole' => function($qu) {
                                $qu->andOnCondition(['ar.exp_name' => 'contents'])
                                ->andOnCondition(['ar.key_role' => 'content']);
                            }])
                                ->where(['c.expansion' => 'contents', 'c.contents_id' => $this->id])
                                ->one();
                    } else {
                        $this->contents = new Contents();
                        $this->contents->expansion = 'contents';
                        $this->contents->bundle = $this->bundle;
                        $this->contents->enabled = $this->getType()->param->enabled;
                        $this->contents->home = $this->getType()->param->home;
                    }
                }
                return $this->contents;
            }

            public function enabled($id, $active) {
                if (!RC::app()->access->roles("contents", "EDIT_CONTENTS")) {
                    if (!is_array($id)) {
                        $id = [$id];
                    }
                    foreach ($id as $k => $v) {
                        if (!RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS', null, ['contents_id' => $v])) {
                            unset($id[$k]);
                        }
                    }
                }

                if (empty($id))
                    return false;
                RC::getCache("exp_contents")->clearTypeFull();
                return Contents::updateAll(['enabled' => $active], ['expansion' => 'contents', 'contents_id' => $id]);
            }

            public function home($id, $active) {
                $home = Contents::find()->joinWith('frontPage')->from(['c' => Contents::tableName()])->where(['c.expansion' => 'contents', 'c.contents_id' => $id])->all();
                foreach ($home as $h) {
                    if (!RC::app()->access->roles("contents", "EDIT_CONTENTS")) {
                        if (!RC::app()->access->roles('contents', 'EDIT_SELF_CONTENTS', null, ['id_user' => $h->id_user])) {
                            continue;
                        }
                    }
                    if ($h->home && $active && !$h->frontPage) {
                        $homeP = new ContentsHome();
                        $homeP->contents_id = $h->contents_id;
                        $homeP->expansion = $h->expansion;
                        $homeP->sort = ContentsHome::find()->where(['expansion' => 'contents'])->count() + 1;
                        $homeP->save();
                    } elseif ($h->home && !$active) {
                        $h->home = $active;
                        $h->save();
                        ContentsHome::deleteAll(['contents_id' => $h->contents_id]);
                    } elseif (!$h->home && $active) {
                        $h->home = $active;
                        $h->save();
                        $homeP = new ContentsHome();
                        $homeP->contents_id = $h->contents_id;
                        $homeP->expansion = $h->expansion;
                        $homeP->sort = ContentsHome::find()->where(['expansion' => 'contents'])->count() + 1;
                        $homeP->save();
                    }
                }
                RC::getCache("exp_contents")->clearTypeFull();
            }

            public function getRoutes() {
                if ($this->routes == null) {
                    if ($this->id) {
                        if ($cont = $this->getContents()) {
                            $this->routes = $cont->route;
                        }
                    } else {
                        $this->routes = new Routes();
                    }

                    $this->routes->expansion = 'contents';
                }
                return $this->routes;
            }

            public function find($id) {
                $this->id = $id;
                $contents = $this->getContents();

                if (!$contents)
                    return false;

                $this->bundle = $contents->bundle;

                $fields = $this->getFields();

                foreach ($fields as $field) {
                    if (!$field->findData(['entry_id' => $contents->contents_id])) {
                        $field->addData();
                    }
                }

                if ($role = $contents->accessRole) {
                    $this->id_role = array_map(function($val) {
                        return $val->id_role;
                    }, $role);
                }


                return true;
            }

            public function getFields() {
                if ($this->bundle && $this->fields == null) {
                    $this->fields = FieldHelper::findAll(['expansion' => 'contents', 'bundle' => $this->bundle, 'active' => 1]);
                }
                return $this->fields;
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

            public function getTitle() {
                $field = $this->getField('title');
                if ($field) {
                    if ($field->data) {
                        return $field->data[0]->title_value;
                    }
                }
            }

            public function resetField() {
                foreach ($this->getFields() as $field) {
                    $field->resetData();
                }
            }

            public function loadAll($data) {

                $this->getContents()->load($data);
                if (!$this->getType()->param->multilang) {
                    $this->contents->id_lang = 0;
                }

                if (isset($data['ModelContent']['id_role'])) {
                    $this->id_role = $data['ModelContent']['id_role'];
                } else {
                    $this->id_role = null;
                }



                $this->getRoutes()->load($data);
                $this->resetField();

                foreach ($this->getFields() as $field) {
                    if (isset($data[$field->field_name])) {
                        $fieldT = $data[$field->field_name];
                        if (ArrayHelper::isIndexed($fieldT)) {
                            foreach ($fieldT as $f) {
                                $f['id_lang'] = $this->contents->id_lang;
                                $field->addData($f);
                            }
                        } else {
                            $fieldT['id_lang'] = $this->contents->id_lang;
                            $field->addData($fieldT);
                        }
                    } else {
                        $field->addData();
                    }
                }
            }

            public function getAllModel() {
                $models = [];

                $models[] = $this->getContents();
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
                        throw new \Exception("EXP_CONTENTS_ROUTEBD_SAVE_ERR");
                    }

                    if ($this->contents->isNewRecord) {

                        $this->contents->routes_id = $this->routes->routes_id;
                        $this->contents->sort = Contents::find()->where(['expansion' => 'contents', 'bundle' => $this->bundle])->count() + 1;
                    }


                    if (!$this->contents->validate() || !$this->contents->save()) {
                        throw new \Exception("EXP_CONTENTS_CONTENTBD_SAVE_ERR");
                    }

                    AccessRole::deleteAll(['exp_name' => 'contents', 'key_role' => 'content', 'key_id' => $this->contents->contents_id]);

                    if (!empty($this->id_role) && is_array($this->id_role)) {
                        foreach ($this->id_role as $id_role) {
                            $role = new AccessRole();
                            $role->exp_name = 'contents';
                            $role->key_role = 'content';
                            $role->key_id = $this->contents->contents_id;
                            $role->id_role = $id_role;
                            if (!$role->save()) {
                                $this->addError('id_role', 'Ошибка сохранения роли пункта  меню');
                                throw new \Exception();
                            }
                        }
                    }

                    $home = ContentsHome::findOne($this->contents->contents_id);

                    if ($this->contents->home && !$home) {
                        $home = new ContentsHome();
                        $home->contents_id = $this->contents->contents_id;
                        $home->expansion = $this->contents->expansion;
                        $home->sort = ContentsHome::find()->where(['expansion' => 'contents'])->count() + 1;
                        $home->save();
                    } elseif (!$this->contents->home && $home) {
                        $home->delete();
                    }

                    foreach ($this->getFields() as $field) {

                        $field->deleteData(['entry_id' => $this->contents->contents_id]);
                        foreach ($field->data as $data) {
                            $data->id_lang = $this->contents->id_lang;
                            $data->entry_id = $this->contents->contents_id;
                            if (!$data->validate() || !$data->save()) {
                                throw new \Exception("EXP_CONTENTS_FIELD_SAVE_ERR");
                            }

                            if ($field->type == 'term') {
                                if (!ContentTermSort::find()->where(['term_id' => $data->term_id, 'contents_id' => $this->contents->contents_id])->exists()) {
                                    $sort = ContentTermSort::find()->where(['term_id' => $data->term_id])->max('sort') + 1;
                                    $sortCatalog = new ContentTermSort();
                                    $sortCatalog->contents_id = $this->contents->contents_id;
                                    $sortCatalog->term_id = $data->term_id;
                                    $sortCatalog->sort = $sort;
                                    if (!$sortCatalog->save()) {
                                        
                                    }
                                }
                            }
                        }
                    }
                    $this->id = $this->contents->contents_id;
                    $transaction->commit();
                    RC::getCache("fw_fields")->clearTypeFull();
                    RC::getCache("exp_contents")->clearTypeFull();
                    RC::getPlugin("contents")->triggerHandler("afterSaveContent", [$this->id, $this]);
                } catch (\Exception $ex) {
                    $this->addError('bundle', $ex->getMessage());
                    $transaction->rollBack();
                    return false;
                }

                return true;
            }

            public function delete() {
                $transaction = RC::getDb()->beginTransaction();

                try {
                    if (!$this->id) {
                        throw new \Exception("ID не может быть пустым");
                    }

                    if (!$this->find($this->id)) {
                        throw new \Exception(Text::_('По текущего ID:{id} ничего не найдено', ['id' => $this->id]));
                    }

                    if (!RC::app()->access->roles("contents", "DELETE_CONTENTS")) {
                        if (!RC::app()->access->roles('contents', 'DELETE_SELF_CONTENTS', null, ['id_user' => $this->getContents()->id_user])) {
                            throw new \Exception(Text::_('По текущего ID:{id} материала у вас нет прав для данного действия', ['id' => $this->id]));
                        }
                    }
                    foreach ($this->getFields() as $field) {
                        $field->deleteData(['entry_id' => $this->contents->contents_id]);
                    }

                    $this->getRoutes()->delete();
                    $this->getContents()->delete();

                    $transaction->commit();
                    RC::getCache("fw_fields")->clearTypeFull();
                    RC::getCache("exp_contents")->clearTypeFull();
                } catch (\Exception $ex) {
                    $this->addError('bundle', $ex->getMessage());
                    $transaction->rollBack();
                    return false;
                }

                return true;
            }

            public function attributeLabels() {
                return[
                    'id_role' => Text::_('EXP_CONTENTS_ACCESS_ROLE'),
                ];
            }

        }
        