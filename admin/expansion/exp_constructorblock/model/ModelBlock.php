<?php

namespace admin\expansion\exp_constructorblock\model;

use maze\base\Model;
use Text;
use RC;
use maze\table\ContentType;
use maze\fields\FieldHelper;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;
use maze\table\Contents;
use maze\table\Routes;
use maze\table\AccessRole;
use admin\expansion\exp_constructorblock\table\Block;
use admin\expansion\exp_constructorblock\table\FilterBlock;
use admin\expansion\exp_constructorblock\table\ViewBlock;
use admin\expansion\exp_constructorblock\table\SortBlock;
use admin\expansion\exp_constructorblock\model\ModelContent;
use maze\db\Query;

class ModelBlock extends Model {

    public function getListExp() {
        $exp = ['contents', 'dictionary'];
        $expType = RC::getDb()->cache(function($db) use ($exp){ 
            return ContentType::find()->where(['expansion' => $exp])->all();
        }, null, 'exp_constructorblock');

        $result = [];
        foreach ($expType as $type) {
            $result[$type->expansion] = RC::getConf(["type" => "expansion", "name" => $type->expansion])->get('name');
        }

        return $result;
    }

    public function getListType($expansion) {

        $result = RC::getDb()->cache(function($db) use ($expansion){
             return ContentType::find()->where(['expansion' => $expansion])->asArray()->all();
        }, null, 'exp_constructorblock');
        return ArrayHelper::map($result, 'bundle', 'title');
    }

    public function getModelFilter($name) {
        try {
            return RC::createObject('admin\expansion\exp_constructorblock\filter\\' . $name . '\Params');
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    public function getModelSort($name) {
        try {
            return RC::createObject('admin\expansion\exp_constructorblock\sort\\' . $name . '\Params');
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getFormFilter($name) {
        $xml = new \XMLConfig(RC::getAlias('@admin/expansion/exp_constructorblock/filter/' . $name . '/meta.options.xml'));

        return $xml;
    }

    public function getTypes($expansion) {
        libxml_use_internal_errors(true);
        $path = RC::getAlias('@admin/expansion/exp_constructorblock/types/' . $expansion . '.xml');
        $xml = simplexml_load_file($path, null, LIBXML_NOCDATA);
        return $xml;
    }

    public function getFilters($expansion, $bundle, $not) {
        $xml = $this->getTypes($expansion);

        $result = [];
        if ($xml) {
            foreach ($xml->fields->field as $key => $val) {

                $filter = $this->getModelFilter((string) $val['filter']);

                if (!$filter)
                    continue;
                if (in_array((string) $val['name'], $not)) {
                    continue;
                }
                $result[] = [
                    'type' => $expansion,
                    'table' => (string) $xml->table,
                    'field' => (string) $val['name'],
                    'filter' => (string) $val['filter'],
                    'label' => (string) $val
                ];
            }
        }

        $fields = FieldHelper::findAll(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.bundle' => $bundle]);

        foreach ($fields as $field) {
            $filter = $this->getModelFilter($field->getType());
            if (!$filter)
                continue;
            if (in_array($field->field_name, $not)) {
                continue;
            }
            $result[] = [
                'type' => 'field',
                'table' => $field->getTableName(),
                'field' => $field->field_name,
                'filter' => $field->getType(),
                'label' => $field->title
            ];
        }

        return $result;
    }

    public function getSorts($expansion, $bundle, $not) {
        libxml_use_internal_errors(true);
        $path = RC::getAlias('@admin/expansion/exp_constructorblock/types/sort.' . $expansion . '.xml');
        $xml = simplexml_load_file($path, null, LIBXML_NOCDATA);
        $result = [];

        if ($xml) {
            foreach ($xml->fields->field as $key => $val) {

                if (in_array($val['name'], $not) || !$this->getModelSort($val['filter'])) {
                    continue;
                }
                $result[] = [
                    'type' => $expansion,
                    'table' => (string) $xml->table,
                    'field' => (string) $val['name'],
                    'filter' => (string) $val['filter'],
                    'label' => (string) $val
                ];
            }
        }

        $fields = FieldHelper::findAll(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.bundle' => $bundle]);

        foreach ($fields as $field) {
            if (in_array($field->field_name, $not) || !$this->getModelSort($field->field_name)) {
                continue;
            }
            $result[] = [
                'type' => 'field',
                'table' => $field->getTableName(),
                'field' => $field->field_name,
                'filter'=>$field->getType(),
                'label' => $field->title
            ];
        }

        return $result;
    }

    public function getFields($expansion, $bundle) {
        $fields = FieldHelper::findAll(['{{%field_exp}}.expansion' => $expansion, '{{%field_exp}}.bundle' => $bundle, '{{%field_exp}}.active' => 1]);
        $result = [];
        foreach ($fields as $field) {
            $result[] = [
                'type' => 'field',
                'field_exp_id' => $field->field_exp_id,
                'table' => $field->getTableName(),
                'field' => $field->field_name,
                'label' => $field->title
            ];
        }

        return $result;
    }

    public function getFieldByID($id) {
        $field = FieldHelper::findByID($id);
        if (!$field) {
            return null;
        }
        return[
            'type' => 'field',
            'expansion' => $field->expansion,
            'bundle' => $field->bundle,
            'field_exp_id' => $field->field_exp_id,
            'table' => $field->getTableName(),
            'field' => $field->field_name,
            'label' => $field->title
        ];
    }

    public function saveBloc($model, $params) {
        $transaction = RC::getDb()->beginTransaction();
        try {

            if (!$model->validate() || !$model->save()) {
                throw new \Exception("Ошибка сохранения модели блока");
            }

            FilterBlock::deleteAll(['code' => $model->code]);
            if (isset($params['FilterBlock'])) {
                foreach ($params['FilterBlock'] as $filter) {
                    $filterModel = new FilterBlock();
                    $filterModel->attributes = $filter;
                    $filterModel->code = $model->code;
                    if ($filterModel->validate()) {
                        if (!$filterModel->save()) {
                            
                        }
                    }
                }
            }

            SortBlock::deleteAll(['code' => $model->code]);
            if (isset($params['SortBlock'])) {
                foreach ($params['SortBlock'] as $sort) {
                    $sortModel = new SortBlock();
                    $sortModel->attributes = $sort;
                    $sortModel->code = $model->code;
                    if ($sortModel->validate()) {
                        if (!$sortModel->save()) {
                            
                        }
                    }
                }
            }

            ViewBlock::deleteAll(['code' => $model->code]);

            if (isset($params['ViewBlock'])) {
                foreach ($params['ViewBlock'] as $key => $view) {
                    $viewModel = new ViewBlock();
                    $viewModel->attributes = $view;
                    $viewModel->code = $model->code;
                    $viewModel->sort = $key + 1;

                    if ($viewModel->validate()) {
                        if (!$viewModel->save()) {
                            
                        }
                    }
                }
            }
            $transaction->commit();
            RC::getCache("exp_constructorblock")->clearTypeFull();
        } catch (\Exception $ex) {
            $model->addError('title', $ex->getMessage());
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function deleteBlock($id) {
        $res = Block::deleteAll(['code' => $id]);
        RC::getCache("exp_constructorblock")->clearTypeFull();
        return $res;
    }

    /**
     * Поиск блока по code
     * 
     * @param string $code - код блока
     * @return array|admin\expansion\exp_constructorblock\model\ModelContent
     */
    public function getBlockByCode($code, $callback = null) {
        $block = RC::getDb()->cache(function($db) use ($code){ 
            return Block::find()
                        ->from(['b' => Block::tableName()])
                        ->joinWith(['filter', 'sort', 'view'])
                        ->where(['b.code' => $code])->one();
        }, null, 'exp_constructorblock');

        if (!$block)
            return false;



        $type = $this->getTypes($block->expansion);
        $tableSelect = (string) $type->table;
        $pkey = (string) $type->primarykey;


        $data = null;
        $query = new Query();
        $query->select([$tableSelect . '.*'])->from($tableSelect);
        $query->where([$tableSelect . '.expansion' => $block->expansion, $tableSelect . '.bundle' => $block->bundle]);

        if ($block->filter) {
            foreach ($block->filter as $filter) {
                if (($fl = $this->getModelFilter($filter->filter))) {
                    $fl->attributes = $filter->queryFilter;
                    $fl->buildQuery($query, $tableSelect, $pkey);
                }
            }
        }
       
        if ($block->sort) {
            foreach ($block->sort as $sort) {
                
                if ($sort->filter && ($sm = $this->getModelSort($sort->filter))) {
                    $sm->attributes = $sort->attributes;
                    $sm->buildQuery($query, $tableSelect, $pkey, $block->expansion, $block->bundle);
                }

            }
        }
        $callback = trim($callback);
        if (!empty($callback)) {
           
            try {
                $callback = create_function('$query', $callback);
                $resultFunc = $callback($query);
                
                if(!$resultFunc){
                    return false;
                }             
                
            } catch (\Exception $exc) {

            }
            
            
        }

        if ($block->list) {

            $query->groupBy($tableSelect . '.' . $pkey);

            $count =  RC::getDb()->cache(function($db) use ($query){ return $query->count(); }, null, 'exp_constructorblock');

            $first = $block->multiple_start;
            if ($block->multiple_start > $count) {
                $first = 0;
            }
            if ($block->multiple_size && ($count - $block->multiple_size) > 0) {
                $query->offset($first)->limit($block->multiple_size);
            } else {
                $query->offset($first);
            }

           
            $data =  RC::getDb()->cache(function($db) use ($query){ return $query->all(); }, null, 'exp_constructorblock');
        } else {

            $data =  RC::getDb()->cache(function($db) use ($query){ return $query->one(); }, null, 'exp_constructorblock');
        }
        

        if (!$block->list) {
            $data = [$data];
        }


        $result = [];

        foreach ($data as $d) {
            $result[] = ModelContent::createModel(['contents' => $d, 'code' => $block->code, 'pkey' => $pkey]);
        }

        if (!$block->list && $result) {
            $result = end($result);
        }

        return $result;
    }

}
