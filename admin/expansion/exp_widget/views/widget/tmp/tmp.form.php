<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;

$this->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
$this->addScript(RC::app()->getExpUrl("/js/form.js"));
?>
<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'widget-form',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
?>
<blockquote>
    <h4><?php echo $xmlParams->get('name'); ?></h4>
    <small><?php echo $xmlParams->get('description'); ?></small>    
</blockquote>
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-widget-tabs']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_WIDGET_FORM_TABS_MASTER")); ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($modelForm, 'title'); ?>
        <?= $form->field($modelForm, 'title_show', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?= $form->field($modelForm, 'enable_cache', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?= $form->field($modelForm, 'time_cache')->element('ui\text\InputSpinner', [ 'settings' => ['min' => 1, 'max' => 1000, 'step' => 1]]); ?>
        <?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', [ 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        <?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', [ 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]); ?>
        <?= $form->field($modelForm, 'time_active')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>
        <?= $form->field($modelForm, 'time_inactive')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>        
    </div>
    <div class="col-md-6">
        <?= $form->field($modelForm, 'param[wrapper]')->label(Text::_('EXP_WIDGET_PARAMS_LABEL_WRAPER'))->element('ui\tmp\WrapWidget', ['front' => $front, 'options' => ['class' => 'form-control', 'prompt' => '-- ' . Text::_('EXP_WIDGET_PARAMS_PROMPT_WRAPER') . ' --']]); ?>
        <?= $form->field($modelForm, 'id_tmp')->element('ui\select\Chosen', ['items' => $model->getTemplate(['front' => $front]), 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_tmp') . ' --']]); ?>
        <?= $form->field($modelForm, 'position')->element('ui\select\Chosen', ['items' => $model->getPosition($modelForm->id_tmp), 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('position') . ' --']]); ?>
        <div class="form-group">
            <label class="control-label"><?= Text::_('EXP_WIDGET_FORM_LABEL_SORTPOSITION') ?></label>

            <ul class="list-group widget-elements-sort">
                <?php if ($postionWidget): ?>
                    <?php foreach ($postionWidget as $key => $pos): ?>
                        <li data-field-id_wid="<?= $pos['id_wid'] ?>" class="list-group-item<?= $pos['id_wid'] == $modelForm->id_wid ? ' active' : ''; ?>"><span aria-hidden="true" class="glyphicon glyphicon-move"></span> <span class="title-widget-sort"><?= $pos['title'] ?></span>
                            <?= Html::hiddenInput('FormWidget[sort][' . $key . ']', $pos['id_wid']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li data-field-id_wid="self" class="list-group-item active"><span aria-hidden="true" class="glyphicon glyphicon-move"></span> <span class="title-widget-sort">....</span>
                        <?= Html::hiddenInput('FormWidget[sort][0]', 'self'); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_WIDGET_FORM_TABS_BIND")); ?>
<div class="row">
    <div class="col-md-6">
        <?=
                $form->field($modelForm, 'id_exp', ['template' => '{label}{hint}{input}', 'hintOptions' => ['class' => 'alert alert-warning']])
                ->hint(Text::_("EXP_WIDGET_FORM_LABEL_APP_HELP"))
                ->element('ui\exp\ExpList', ['condition' => ['front_back' => $front], 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
        <?php if ($front): ?>
            <?=
                    $form->field($modelForm, 'id_menu', ['template' => '{label}{hint}{input}', 'hintOptions' => ['class' => 'alert alert-warning']])
                    ->hint(Text::_("EXP_WIDGET_FORM_LABEL_MENU_HELP"))
                    ->element('ui\menu\ItemsTree', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
            ?>
            <div class="form-group">
                <div class="btn-group" role="group">
                    <button type="button" onclick="$('#widget-form .maze-select-tree-wrap').jstree('open_all')" class="btn btn-default"><?= Text::_('EXP_WIDGET_TREE_MENU_OPEN_ALL') ?></button>
                    <button type="button" onclick="$('#widget-form .maze-select-tree-wrap').jstree('close_all')" class="btn btn-default"><?= Text::_('EXP_WIDGET_TREE_MENU_CLOSE_ALL') ?></button>
                    <button type="button" onclick="$('#widget-form .maze-select-tree-wrap').jstree('select_all')" class="btn btn-default"><?= Text::_('EXP_WIDGET_TREE_MENU_SELECT_ALL') ?></button>
                    <button type="button" onclick="$('#widget-form .maze-select-tree-wrap').jstree('deselect_all', true)" class="btn btn-default"><?= Text::_('EXP_WIDGET_TREE_MENU_DESELECT_ALL') ?></button>
                </div>
            </div>

        <?php endif; ?>


    </div>
    <div class="col-md-6">
        <?= $form->field($modelForm, 'enable_php', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?=
                $form->field($modelForm, 'php_code', ['template' => '{hint}{input}', 'hintOptions' => ['class' => 'alert alert-warning']])
                ->hint(Text::_("EXP_WIDGET_FORM_LABEL_PHPCODE_HELP"))
                ->element('ui\editor\Code', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>

    </div>
</div>
<div>
    <div class="form-group">
        <label class="control-label"><?= Text::_('EXP_WIDGET_FORM_LABEL_URLCONDITION') ?></label>

        <div class="alert alert-warning"><?= Text::_('EXP_WIDGET_FORM_HELP_URLCONDITION') ?></div>
        <div class="widget-condition-url-block">
            <?php if ($modelForm->url): ?>
                <?php foreach ($modelForm->url as $key => $val): ?>
                    <div class="form-group widget-condition-url">
                        <div class="input-group">
                            <span class="input-group-addon widget-condition-url-sort"><span aria-hidden="true" class="glyphicon glyphicon-move"></span></span>
                            <span class="input-group-btn">
                                <?= Html::activeDropDownList($modelForm, 'url[' . $key . '][method]', ['get' => 'GET', 'post' => 'POST', 'url' => 'URL'], ['class' => 'form-control widget-condition-url-method', 'style' => 'width:100px']) ?>
                            </span>
                            <?= Html::activeTextInput($modelForm, 'url[' . $key . '][name]', ['class' => 'form-control widget-condition-url-param' . ($val['method'] == 'url' ? ' hide' : ''), 'placeholder' => Text::_('EXP_WIDGET_URL_PARAMNAME')]) ?>

                            <span class="input-group-addon widget-condition-url-equally<?= ($val['method'] == 'url' ? ' hide' : '') ?>">=</span>
                            <?= Html::activeTextInput($modelForm, 'url[' . $key . '][value]', ['class' => 'form-control widget-condition-url-value', 'placeholder' => Text::_('EXP_WIDGET_URL_PARAMVALUE')]) ?>

                            <span class="input-group-btn">
                                <?= Html::activeDropDownList($modelForm, 'url[' . $key . '][visible]', ['1' => Text::_('EXP_WIDGET_VISIBLE'), '0' => Text::_('EXP_WIDGET_HIDE')], ['class' => 'form-control', 'style' => 'width:100px']) ?>
                            </span>
                            <span class="input-group-btn">
                                <button class="btn btn-danger widget-condition-url-delete" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></button>                        
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="form-group widget-condition-url">
                    <div class="input-group">
                        <span class="input-group-addon widget-condition-url-sort"><span aria-hidden="true" class="glyphicon glyphicon-move"></span></span>
                        <span class="input-group-btn">
                            <?= Html::activeDropDownList($modelForm, 'url[0][method]', ['get' => 'GET', 'post' => 'POST', 'url' => 'URL'], ['class' => 'form-control widget-condition-url-method', 'style' => 'width:100px']) ?>
                        </span>
                        <?= Html::activeTextInput($modelForm, 'url[0][name]', ['class' => 'form-control widget-condition-url-param', 'placeholder' => Text::_('EXP_WIDGET_URL_PARAMNAME')]) ?>

                        <span class="input-group-addon widget-condition-url-equally">=</span>
                        <?= Html::activeTextInput($modelForm, 'url[0][value]', ['class' => 'form-control widget-condition-url-value', 'placeholder' => Text::_('EXP_WIDGET_URL_PARAMVALUE')]) ?>

                        <span class="input-group-btn">
                            <?= Html::activeDropDownList($modelForm, 'url[0][visible]', ['1' => Text::_('EXP_WIDGET_VISIBLE'), '0' => Text::_('EXP_WIDGET_HIDE')], ['class' => 'form-control', 'style' => 'width:100px']) ?>
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-danger widget-condition-url-delete" type="button"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span></button>                        
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <div class="form-group">
        <button class="btn btn-default btn-block" id="widget-form-btn-add-url" type="button"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span><?= Text::_('EXP_WIDGET_BTN_ADD_CONDITION') ?></button>
    </div>
</div>
<?php $tabs->endTab(); ?>

<?php if (isset($params->accordion)): ?>
    <?php $tabs->beginTab(Text::_("EXP_WIDGET_FORM_TABS_WIDGETS")); ?>
    <?php $acc = JqAccordion::begin(['options' => ['id' => 'admin-accordion-widgets']]); ?> 
    <?php foreach ($params->accordion as $fielset): ?>
        <?php $acc->beginTab(Text::_($fielset["title"])); ?>

            <?php foreach ($fielset as $element): ?>
                <div class="form-group">
                    <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions'=>['class'=>'control-label']]); ?>
                    <?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'param')) ?>
                </div>
            <?php endforeach; ?>

        <?php $acc->endTab(); ?>
    <?php endforeach; ?>
    <?php JqAccordion::end(); ?>
    <?php $tabs->endTab(); ?>
<?php endif; ?>

<?php JqTabs::end(); ?>

<?php ui\form\FormBuilder::end(); ?>  