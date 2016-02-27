<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\fields\FieldHelper;
use maze\helpers\ArrayHelper;
use maze\helpers\Json;

$this->addStylesheet(RC::app()->getExpUrl('css/style.css'));
$this->addScript(RC::app()->getExpUrl("/js/form.js"));
?>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'constructorblock-form',
                'groupClass' => 'form-group',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}'
    ]);
    ?>
    <div class="row">
        <div class="col-sm-8 col-md-8"><?= $form->field($model, 'title'); ?></div>
        <div class="col-sm-4 col-md-4"><?= $form->field($model, 'code')->textInput(['disabled' => (!$model->isNewRecord)]); ?></div>
    </div>

    <?= $form->field($model, 'description')->textarea(); ?>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <?= $form->field($model, 'expansion')->element('ui\select\Chosen', ['items' => $modelHelp->listExp, 'options' => ['class' => 'form-control', 'prompt' => '-- ' . Text::_('EXP_CONSTRUCTORBLOCK_BLOCK_EXPANSION_LABEL') . ' --']]); ?> 
        </div>
        <div class="col-sm-6 col-md-6">
            <?= $form->field($model, 'bundle')->element('ui\select\Chosen', ['items' => $modelHelp->getListType($model->expansion), 'options' => ['class' => 'form-control', 'prompt' => '-- ' . Text::_('EXP_CONSTRUCTORBLOCK_BLOCK_BUNDLE_LABEL') . ' --']]); ?> 
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default" id="constructorblock-filter-field">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Text::_('EXP_CONSTRUCTORBLOCK_FILTER_LABEL') ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <?php if ($model->filter): ?>
                            <?php foreach ($model->filter as $filter): ?>
                                <li <?= Html::renderTagAttributes(['data-param' => Json::encode(ArrayHelper::toArray($filter))]); ?> class="list-group-item">
                                    <?= $filter->label ?> <small>[поле: <?= $filter->field ?>]</small> 
                                    <div class="btn-group edit-filter-btn" role="group">
                                        <button onclick="return editConditionFilter(this);" type="button" class="btn btn-default"><span aria-hidden="true" class="glyphicon glyphicon-pencil"></span></button> 
                                        <button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary btn-block bundle-block action-btn"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> <?= Text::_('EXP_CONSTRUCTORBLOCK_ADDACTION') ?></button>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default" id="constructorblock-sort-field">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Text::_('EXP_CONSTRUCTORBLOCK_SORT_LABEL') ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <?php if ($model->sort): ?>
                            <?php foreach ($model->sort as $sort): ?>
                                <li <?= Html::renderTagAttributes(['data-param' => Json::encode(ArrayHelper::toArray($sort))]); ?> class="list-group-item">
                                    <?= $sort->label ?> <small>[поле: <?= $sort->field ?>]</small> 
                                    <div class="input-group edit-filter-btn">
                                        <?=  Html::dropDownList('', $sort->order, ['ASC'=>'ASC', 'DESC'=>'DESC'], ['onchange'=>'return sortConditionOrder(this);', 'class'=>'form-control'])?>
                                        <span class="input-group-btn">
                                            <button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary btn-block bundle-block action-btn"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> <?= Text::_('EXP_CONSTRUCTORBLOCK_ADDACTION') ?></button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default" id="constructorblock-field-field">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Text::_('EXP_CONSTRUCTORBLOCK_FIELD_LABEL') ?></h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <?php if ($model->view): ?>
                            <?php foreach ($model->view as $view): ?>
                                <li <?php 
                                   $attr = ArrayHelper::toArray($view);
                                   $attr['field'] = $modelHelp->getFieldByID($view->field_exp_id);
                                echo  Html::renderTagAttributes(['data-param' => Json::encode($attr)]); ?> class="list-group-item">
                                    <?= $attr['field']['label'] ?> <small>[поле: <?= $attr['field']['field']?>]</small> 
                                    <div class="btn-group edit-filter-btn" role="group">
                                        <button onclick="return editConditionField(this);" type="button" class="btn btn-default"><span aria-hidden="true" class="glyphicon glyphicon-pencil"></span></button> 
                                        <button type="button" onclick="return deleteCondition(this);" class="btn btn-danger"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <button type="button" class="btn btn-primary btn-block bundle-block action-btn"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> <?= Text::_('EXP_CONSTRUCTORBLOCK_ADDACTION') ?></button>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="panel panel-default" id="constructorblock-field-field">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Text::_('EXP_CONSTRUCTORBLOCK_VIEW_LABEL') ?></h3>
                </div>
                <div class="panel-body">
                    <?= $form->field($model, 'list', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                    <div class="row">
                        <div class="col-sm-6 col-md-6">
                            <?= $form->field($model, 'multiple_size'); ?> 
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <?= $form->field($model, 'multiple_start'); ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ui\form\FormBuilder::end(); ?>
</div>

   