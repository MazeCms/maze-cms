<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\helpers\Json;
use ui\tabs\JqTabs;

?>

<script>
    jQuery().ready(function () {
        $('#viewblock-field_view').change(function () {
            var url = $(this).closest('form').attr('action');
            url += '&params[field_view]='+$(this).val();
            var $self = $(this);
            $.get(url, function(data){

                $self.closest('.maze-content').children('div').html(data.html)
                cms.loadHeader(data)
            }, 'json')
        })
    })
</script>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'constructorblock-field-form',
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
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-field-view-tabs-contents']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_CONTENTS_FIELD_FORM_ONERFIELD")); ?> 

 <?= $form->field($modelView, 'field_view')->element('ui\select\Chosen', ['items' => \maze\fields\FieldHelper::listFieldView($field->type), 'options' => ['class' => 'form-control', 'prompt' => '-- Виджет поля --']]); ?>

<div class="row">
    <div class="col-sm-6 col-md-6">
        <?= $form->field($modelView, 'show_label', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
    </div>
    <div class="col-sm-6 col-md-6">
        <?= $form->field($modelView, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6 col-md-6">
        <?= $form->field($modelView, 'class_wrapper'); ?>
    </div>
    <div class="col-sm-6 col-md-6">
        <?=
        $form->field($modelView, 'tag_wrapper')->element('ui\select\Chosen', ['items' => [
                'h1' => 'h1',
                'h2' => 'h2',
                'h3' => 'h3',
                'h4' => 'h4',
                'h5' => 'h5',
                'h6' => 'h6',
                'span' => 'span',
                'div' => 'div',
                'strong' => 'strong',
                'p' => 'p',
                'li'=>'li'
            ], 'options' => ['class' => 'form-control', 'prompt' => '--  HTML Тег обертки поля --']]);
        ?>
    </div>
</div>
<?php if ($field->many_value > 1 || $field->many_value == 0): ?>
    <div class="row">
        <div class="col-sm-6 col-md-6">
    <?= $form->field($modelView, 'multiple_size'); ?>
        </div>
        <div class="col-sm-6 col-md-6">
    <?= $form->field($modelView, 'multiple_start'); ?>
        </div>
    </div>
<?php endif; ?>
<?php $tabs->endTab(); ?>
<?php if ($fieldViewModel && $metaView->getParams()): ?>
 <?php $tabs->beginTab(Text::_("Настройки вида поля")); ?>
    <div class="form-horizontal">
        <?php foreach ($metaView->getParams()->element as $element): ?>
            <?php echo $form->beginField($fieldViewModel, $element['name']); ?>
            <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? Text::_($element['description']) : null), 'htmlOptions' => ['class' => 'col-sm-3 control-label']]); ?>
            <div class="col-sm-9"><?php echo $metaView->elemenet($element, $fieldViewModel) ?></div>
            <?= Html::error($fieldViewModel, $element['name']); ?>
            <?php echo $form->endField(); ?>
    <?php endforeach; ?>
    </div>
    <?php $tabs->endTab(); ?>
<?php endif; ?>
<?php JqTabs::end(); ?>

<?php ui\form\FormBuilder::end(); ?>
