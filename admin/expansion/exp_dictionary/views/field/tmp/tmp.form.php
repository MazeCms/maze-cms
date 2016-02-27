<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
?>
<script>
    jQuery().ready(function () {
        $('#field-widget_name').change(function () {
            cms.redirect([{
                    widget_name: $(this).val()
                }]);
        })
    })
</script>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'dictionary-form-field',
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

<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-tabs-dictionary']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_DICTIONARY_FIELD_FORM_ONERFIELD")); ?>  

<div class="row">
    <div class="col-sm-8 col-md-8">
        <?= $form->field($field, 'widget_name')->element('ui\select\Chosen', ['items' => $widgets, 'options' => ['class' => 'form-control', 'prompt' => '-- Виджет поля --']]); ?>
    </div>
    <div class="col-sm-4 col-md-4"></div>
</div>
<div class="row">
    <div class="col-sm-8 col-md-8">
        <?= $form->field($field, 'title'); ?>
    </div>
    <div class="col-sm-4 col-md-4">
        <?= $form->field($field, 'field_name')->textInput(['disabled' => (!$field->isNew)]); ?>
    </div>
</div>
<?= $form->field($field, 'prompt')->textarea(); ?>
<?= $form->field($field, 'many_value')->element('ui\text\InputSpinner', ['settings' => ['min' => 0, 'max' => 100]]); ?>
 <?= $form->field($field, 'active', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
<?php $tabs->endTab(); ?>

<?php if ($field->config): ?>
    <?php $tabs->beginTab(Text::_("EXP_DICTIONARY_FIELD_FORM_FIELD")); ?> 

    <div class="form-horizontal">
        <?php foreach ($field->config->getParams()->element as $element): ?>
            <?php echo $form->beginField($field->settings, $element['name']); ?>
            <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions' => ['class' => 'col-sm-3 control-label']]); ?>
            <div class="col-sm-9"><?php echo $field->config->elemenet($element, $field->settings) ?></div>
            <?= Html::error($field->settings, $element['name']); ?>
            <?php echo $form->endField(); ?>
        <?php endforeach; ?>
    </div>

    <?php $tabs->endTab(); ?>
<?php endif; ?>

<?php if ($field->configWidget && $field->configWidget->getParams()): ?>
    <?php $tabs->beginTab(Text::_("EXP_DICTIONARY_FIELD_FORM_WID")); ?> 

    <div class="form-horizontal">
        <?php foreach ($field->configWidget->getParams()->element as $element): ?>
            <?php echo $form->beginField($field->widget, $element['name']); ?>
            <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions' => ['class' => 'col-sm-3 control-label']]); ?>
            <div class="col-sm-9"><?php echo $field->configWidget->elemenet($element, $field->widget) ?></div>
            <?php echo $form->endField(); ?>
        <?php endforeach; ?>
    </div>

    <?php $tabs->endTab(); ?>
<?php endif; ?>


<?php JqTabs::end(); ?>  
<?php ui\form\FormBuilder::end(); ?>
