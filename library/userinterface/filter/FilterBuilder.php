<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormsBuilder
 *
 * @author Nikolas-link
 */

namespace ui\filter;

use ui\Elements;
use maze\helpers\Html;
use maze\helpers\ArrayHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use ui\filter\FilterField;
use maze\table\Filters;
use ui\filter\Model;
use maze\table\FiltersFields;

class FilterBuilder extends Elements {

    /**
     * @var string  $action - url формы
     */
    public $action = '';

    /**
     * @var string  $method - метод отправки формы
     */
    public $method = 'post';

    /**
     * @var array  $options - настройки формы
     */
    public $options = [];

    /**
     *
     * @var object - модель фильтра
     */
    public $model;

    /**
     * @var array  $_fields - массив объектов элементов формы
     */
    public $_fields;

    /**
     * @var array  $fieldConfig - анонимная функция создания полей формы
     */
    public $fieldConfig = [];

    /**
     * @var string  $fieldClass - класс с элементами формы
     */
    public $fieldClass = 'ui\filter\FilterField';

    /**
     * @var array  настройки полей фильтра 
     */
    public $attributes = [];

    /**
     * @var string  $varCheck переменная отправляема при ajax проверке формы
     */
    public $varCheck = 'checkform';

    /**
     * @var array  $groupClass группа элементов формы
     */
    public $groupClass = 'form-group';

    /**
     * @var string  $dataFilter js function(data){ return data}
     */
    public $dataFilter;

    /**
     * @var array  $paramsAjax одополнительные параметры отсылаеме при ajax запросе валидации
     */
    public $paramsAjax = [];

    /**
     * @var string  callback
     */
    public $onFilter;
    public $onReset;

    /**
     * @var string - класс обертки фильтра 
     */
    public $classWrapp = 'filter-wrapp';

    /**
     * @var ActiveRecord - данные из БД по текущему фильтру 
     */
    protected $data;

    public function init() {

        $refModel = new \ReflectionClass($this->model);

        if (!$refModel->isSubclassOf('ui\filter\Model')) {
            throw new \Exception('Модель должна наследовать класс FilterField.');
        }

        if (!$this->_id) {
            $this->_id = strtoupper(\RC::app()->router->component . $this->model->formName());
        }

        $this->model->component = \RC::app()->router->component;
        $this->model->code = $this->getId();
        $condition = ['enabled' => 1, 'component' => $this->model->component,
            'code' => $this->model->code, 'id_user' => \RC::app()->access->getUid()];

        $filter_id = \Request::getVar('filter_id');
        if ($filter_id) {
            Filters::updateAll(['defaults' => 0], $condition);
            $filter = Filters::findOne($filter_id);
            if ($filter) {
                $filter->defaults = 1;
                $filter->save();
            }
        }

        $filter = Filters::find()->where($condition)->orderBy('sort')->all();
        echo Html::beginTag('div', ['class' => $this->classWrapp, 'id' => $this->getId()]);

        echo '<ul class="filter-form-tabs-link">';
        if ($filter) {
            $current = \URI::current();
            foreach ($filter as $fil) {
                $class = $fil->defaults ? ' class="active" ' : '';
                $url = new \URI($current);
                $url->setVar('filter_id', $fil->filter_id);
                echo '<li' . $class . '><a href="' . $url . '">' . $fil->title . '</a></li>';
                if ($fil->defaults) {
                    $this->model->filter_id = $fil->filter_id;
                    $this->data = $fil->fields;
                    foreach ($this->data as $field) {

                        if ($this->model->hasProperty($field->field)) {
                            $this->model->offsetSet($field->field, $field->datavalue);
                        }
                    }
                }
            }
        }

        \RC::app()->toolbar->addGroup('filter', [
            'class' => '\Buttonset',
            "TITLE" => "Фильтр",
            "TYPE" => \Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => true,
            "SORTGROUP" => 1,
            "SRC" => "/library/jquery/toolbarsite/images/icon-filter.png",
            "ACTION" => "$('#" . $this->getId() . "').filterBuilder('filter'); return false;",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Сбросить',
                    "SORT" => 3,
                    "SRC" => "/library/jquery/toolbarsite/images/icon-close.png",
                    "ACTION" => "$('#" . $this->getId() . "').filterBuilder('reset'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Сохранить',
                    "SORT" => 4,
                    "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy-16.png",
                    "ACTION" => "$('#" . $this->getId() . "').filterBuilder('save'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Удалить',
                    "SORT" => 2,
                    "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
                    "ACTION" => "$('#" . $this->getId() . "').filterBuilder('delete'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Переименовать',
                    "SORT" => 1,
                    "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
                    "ACTION" => "$('#" . $this->getId() . "').filterBuilder('rename'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Скрыть - показать',
                    "SORT" => 5,
                    "SRC" => "/library/jquery/toolbarsite/images/asinhron.png",
                    "ACTION" => "$('#" . $this->getId() . "').filterBuilder('toggle'); return false;"
                ]
                
            ]
        ]);

        echo '<li><a class="filter-form-trigger-add" href="javaScript:void(0)"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span></a></li>';
        echo '</ul>';

        echo Html::beginForm($this->action, $this->method, $this->options);
        echo '<div class="filter-top-bar">';
        echo Html::button('<span aria-hidden="true" class="glyphicon glyphicon-cog"></span>', ['class' => 'btn btn-default filter-form-visible-list']);
        echo '</div>';
    }

    public function run() {

        if (!empty($this->_fields)) {
            throw new \Exception('Каждый beginField() должен иметь соответствующего ему вызов endField().');
        }

        if ($this->onFilter) {
            $options['onFilter'] = new JsExpression($this->onFilter);
        }

        if ($this->onReset) {
            $options['onReset'] = new JsExpression($this->onReset);
        }

        $options['elemFilter'] = $this->attributes;

        \Document::instance()->setTextScritp('$("#' . $this->getId() . '").filterBuilder(' . Json::encode($options) . ');',['wrap'=>\Document::DOCREADY]);

        echo $this->field('component', ['template' => '{input}'])->hiddenInput();
        echo $this->field('filter_id', ['template' => '{input}'])->hiddenInput();
        echo $this->field('code', ['template' => '{input}'])->hiddenInput();
        echo Html::endForm();
        echo '<div class="filter-form-toggle">...</div>';
        echo Html::endTag('div');
    }

    public function registerClientScript() {
        $doc = \RC::app()->document;
        $doc->addScript("/library/jquery/filter/filter-builder.js");
    }

    public function field($attribute, $options = []) {
        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $this->model, $attribute);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }

        if ($this->data && isset($this->data[$attribute])) {
            $options['visible'] = $this->data[$attribute]->visible;
        }

        return \RC::createObject(ArrayHelper::merge($config, $options, [
                            'model' => $this->model,
                            'attribute' => $attribute,
                            'form' => $this,
        ]));
    }

    public function beginField($attribute, $options = []) {
        $field = $this->field($attribute, $options);
        $this->_fields[] = $field;
        return $field->begin();
    }

    public function endField() {
        $field = array_pop($this->_fields);
        if ($field instanceof FilterField) {
            return $field->end();
        } else {
            throw new \Exception('Несовпадение  вызова endField()');
        }
    }

    public static function action($model) {

        $request = \RC::app()->request;
        $cmd = $request->get('filtercmd');
        if ($request->isPost() && $request->post($model->formName())) {
            $model->load($request->post());
            $model->validate();
            switch ($cmd) {
                case 'save':
                    if ($model->filter_id) {
                        $filter = Filters::findOne($model->filter_id);
                    } else {
                        $filter = new Filters();
                        $filter->code = $model->code;
                        $filter->component = $model->component;
                        $filter->title = "Новая вкладка";
                        $filter->save();
                    }
                    if (!$filter)
                        return;
                    FiltersFields::deleteAll(['filter_id' => $model->filter_id]);
                    foreach ($model->attributes as $name => $value) {
                        if (in_array($name, ['component', 'filter_id', 'code', 'visible']))
                            continue;

                        $field = new FiltersFields();
                        $field->filter_id = $filter->filter_id;
                        $field->field = $name;
                        $field->visible = isset($model->visible[$name]) ? 1 : 0;
                        $field->datavalue = $field->visible ? $value : '';
                        $field->save();
                    }
                    $attributes = $filter->attributes;
                    $url = new \URI(\URI::current());
                    $url->setVar('filter_id', $attributes['filter_id']);
                    $url->delVar('filtercmd');
                    $url->delVar('clear');
                    $attributes['urlfilter'] = $url->toString();
                    return ['attributes' => $attributes];
                    break;
                case 'add':
                    $filter = new Filters();
                    $filter->code = $model->code;
                    $filter->component = $model->component;
                    $filter->title = "Новая вкладка";
                    $filter->save();
                    foreach ($model->attributes as $name => $value) {
                        if (in_array($name, ['component', 'filter_id', 'code', 'visible']))
                            continue;

                        $field = new FiltersFields();
                        $field->filter_id = $filter->filter_id;
                        $field->field = $name;
                        $field->visible = isset($model->visible[$name]) ? 1 : 0;
                        $field->datavalue = $field->visible ? $value : '';
                        $field->save();
                    }
                    $attributes = $filter->attributes;
                    $url = new \URI(\URI::current());
                    $url->setVar('filter_id', $attributes['filter_id']);
                    $url->delVar('filtercmd');
                    $url->delVar('clear');
                    $attributes['urlfilter'] = $url->toString();
                    return ['attributes' => $attributes];

                    break;
                case 'delete':
                    if ($model->filter_id) {
                        $filter = Filters::findOne($model->filter_id);
                        $filter->delete();

                        $condition = ['enabled' => 1, 'component' => $model->component,
                            'code' => $model->code, 'id_user' => \RC::app()->access->getUid()];
                        $filter = Filters::findOne($condition);
                        if ($filter) {
                            $filter->defaults = 1;
                            $filter->save();
                        }
                    }
                    return ['attributes' => $filter->attributes];
                    break;
                case 'rename':
                    if ($model->filter_id && $request->post('newname')) {
                        $filter = Filters::findOne($model->filter_id);
                        if($filter)
                        {
                            $filter->title = $request->post('newname');
                            $filter->save();
                        }
                    }
                    return ['attributes' => $model->attributes];
                    break;
                case 'visible':
                    if (!$model->filter_id)
                        return;
                    foreach ($model->attributes as $name => $value) {
                        if (in_array($name, ['component', 'filter_id', 'code', 'visible']))
                            continue;
                        $field = FiltersFields::find()->where(['filter_id' => $model->filter_id, 'field' => $name])->one();
                        if (!$field) {
                            $field = new FiltersFields();
                            $field->filter_id = $model->filter_id;
                            $field->field = $name;
                            $field->datavalue = isset($model->visible[$name]) ? $value : '';
                        }

                        $field->visible = isset($model->visible[$name]) ? 1 : 0;
                        $field->save();
                    }
                    return ['attributes' => $model->attributes];
                    break;
                default :
                    foreach ($model->attributes as $name => $value) {
                        if ($model->hasProperty($name)) {
                            $model->offsetSet($name, (isset($model->visible[$name]) ? $model->offsetGet($name) : null));
                        }
                    }
                    break;
            }
        } elseif ($request->isPost()) {
            if ($request->get('filter_id')) {
                $field = FiltersFields::find()->where(['filter_id' => $request->get('filter_id')])->all();
            } else {

                $model->component = \RC::app()->router->component;
                $model->code = strtoupper(\RC::app()->router->component . $model->formName());
                $condition = ['enabled' => 1, 'component' => $model->component,
                    'code' => $model->code, 'id_user' => \RC::app()->access->getUid(), 'defaults' => 1];
                $filter = Filters::find()->where($condition)->one();
                $field = $filter ? $filter->fields : null;
            }

            if ($field) {
                foreach ($field as $fl) {

                    if ($model->hasProperty($fl->field)) {
                        $model->offsetSet($fl->field, ($fl->visible ? $fl->datavalue : null));
                    }
                }
            }
        }

        return false;
    }

}
