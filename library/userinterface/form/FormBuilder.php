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

namespace ui\form;
use ui\Elements; 
use maze\base\Model;
use maze\helpers\Html;
use maze\helpers\ArrayHelper;
use maze\base\JsExpression;
use maze\helpers\Json;

class FormBuilder extends Elements {

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
     * @var bool  $enableClientValidation - разрешить проверку элементов формы на клиенте
     */
    public $enableClientValidation = true;

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
    public $fieldClass = 'ui\form\FormField';

    /**
     * @var array  настройки валидатора формы
     */
    public $attributes = [];

    /**
     * @var string  css class Обязательные атрибуты
     */
    public $requiredCssClass = 'required-group';

    /**
     * @var string  css class группы с ошибками
     */
    public $errorCssClass = 'has-error';

    /**
     * @var string  css class всех ошибок
     */
    public $errorSummaryCssClass = 'error-summary alert-danger';

    /**
     * @var string  $successCssClass css class при успехе валидации
     */
    public $successCssClass = 'has-success';

    /**
     * @var string  $eventCheck события при кторых происходит проверка формы
     */
    public $eventCheck = 'submit change';

    /**
     * @var bool  $ajaxSubmit проверять форму ajax
     */
    public $ajaxSubmit = true;

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
    public $onAfterCheck;

    /**
     * @var string  callback
     */
    public $onBeforeCheck;
    /**
     * @var string  callback
     */
    public $onBeforeSubmit;

    /**
     * @var string  callback
     */
    public $onErrorAjax;

    /**
     * @var string  callback
     */
    public $onErrorElem;

    /**
     * @var string  callback
     */
    public $onSuccessElem;

    /**
     * @var string  callback
     */
    public $onReset;
    
    public $validationUrl;

    public function init() {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
       
        echo Html::beginForm($this->action, $this->method, $this->options);
    }

    public function run() {
        if (!empty($this->_fields)) {
            throw new \Exception('Each beginField() should have a matching endField() call.');
        }
        if ($this->enableClientValidation) {
            $id = $this->options['id'];
            $options = $this->getClientOptions();
            $options['elements'] = $this->attributes;
            $attributes = Json::encode($options);
            \Document::instance()->setTextScritp('$("#'.$id.'").mazeForm('.$attributes.');',['wrap'=>\Document::DOCREADY]);

        }
        echo Html::endForm();
    }

    protected function getClientOptions() {
        $options = [
            'groupClass'=>'.' . implode('.', preg_split('/\s+/', $this->groupClass, -1, PREG_SPLIT_NO_EMPTY))
        ];
        if(!is_object($this->errorSummaryCssClass))
        {
           $options['errorSummary'] = '.' . implode('.', preg_split('/\s+/', $this->errorSummaryCssClass, -1, PREG_SPLIT_NO_EMPTY));
        }
        else
        {
            $options['errorSummary'] = $this->errorSummaryCssClass;
        }
       
        if($this->eventCheck)
        {
            $options['eventCheck'] = $this->eventCheck;
        }
        if($this->errorCssClass !== null)
        {
            $options['groupError'] = $this->errorCssClass;
        }
        if($this->successCssClass !== null)
        {
            $options['groupSuccess'] = $this->successCssClass;
        }
        if($this->ajaxSubmit !== null)
        {
            $options['ajaxSubmit'] = $this->ajaxSubmit;
        }
        if($this->varCheck !== null)
        {
            $options['varCheck'] = $this->varCheck;
        }
        
        if($this->dataFilter)
        {
            $options['filter'] = new JsExpression($this->dataFilter);
        }
        if($this->onAfterCheck)
        {
            $options['onAfterCheck'] = new JsExpression($this->onAfterCheck);
        }
        if($this->onBeforeCheck)
        {
            $options['onBeforeCheck'] = new JsExpression($this->onBeforeCheck);
        }
        if($this->onBeforeSubmit){
            $options['onBeforeSubmit'] = new JsExpression($this->onBeforeSubmit);
        }
        if($this->onErrorAjax)
        {
            $options['onErrorAjax'] = new JsExpression($this->onErrorAjax);
        }
        if($this->onErrorElem)
        {
            $options['onErrorElem'] = new JsExpression($this->onErrorElem);
        }
        if($this->onSuccessElem)
        {
            $options['onSuccessElem'] = new JsExpression($this->onSuccessElem);
        }
        if($this->onReset)
        {
            $options['onReset'] = new JsExpression($this->onReset);
        }
        if ($this->validationUrl !== null) {
            $options['action'] = (new \URI($this->validationUrl))->toString(['path', 'query', 'fragment']);
        }
        
        if(!empty($this->paramsAjax))
        {
             $options['paramsAjax'] = $this->paramsAjax;
        }

        return array_diff_assoc($options, [
            'eventCheck' => 'submit change',
            'groupError' => 'has-error',
            'groupSuccess' => 'has-success',
            'ajaxSubmit' => true,
            'varCheck' => 'checkform',
            'paramsAjax'=>['clear'=>'ajax'],
            'filter'=> new JsExpression('function(data){ return data}'),
            'onAfterCheck'=>new JsExpression('function (errors, e) { return true; }'),
            'onBeforeCheck'=>new JsExpression('function () { return true; }'),
            'onErrorAjax'=>new JsExpression('$.noop'),
            'onErrorElem'=>new JsExpression('$.noop'),
            'onSuccessElem'=>new JsExpression('$.noop'),
            'onReset'=>new JsExpression('$.noop')
        ]);
    }
    public function registerClientScript() {
        \ui\assets\AssetValidForm::register();
    }
    public function errorSummary($models, $options = []) {
        if(is_string($this->errorSummaryCssClass))
        {
             Html::addCssClass($options, $this->errorSummaryCssClass);
             return Html::errorSummary($models, $options); 
        }
    }

    public function field($model, $attribute, $options = []) {
        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $model, $attribute);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }
        return \RC::createObject(ArrayHelper::merge($config, $options, [
                            'model' => $model,
                            'attribute' => $attribute,
                            'form' => $this,
        ]));
    }

    public function beginField($model, $attribute, $options = []) {
        $field = $this->field($model, $attribute, $options);
        $this->_fields[] = $field;
        return $field->begin();
    }

    public function endField() {
        $field = array_pop($this->_fields);
        if ($field instanceof FormField) {
            return $field->end();
        } else {
            throw new \Exception('Mismatching endField() call.');
        }
    }

    public static function validate($model, $attributes = null) {
        $result = [];
        if ($attributes instanceof Model) {
            $models = func_get_args();
            $attributes = null;
        } else {
            $models = [$model];
        }

        foreach ($models as $model) {
            $model->validate($attributes);
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }
        }
        return $result;
    }

    public static function validateMultiple($models, $attributes = null) {
        $result = [];
        /* @var $model Model */
        foreach ($models as $i => $model) {
            $model->validate($attributes);
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, "[$i]" . $attribute)] = $errors;
            }
        }
        return $result;
    }

}
