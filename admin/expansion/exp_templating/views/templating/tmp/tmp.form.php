<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'templating-style-form',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
?>
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-tabs-templating-style']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_TEMPLATING_STYLE_FORM_TABS_ONE")); ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($modelForm, 'title'); ?>
        <?= $form->field($modelForm, 'name')->element('ui\tmp\TemplateImage', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('name') . ' --']]); ?>
        <?= $form->field($modelForm, 'home', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <?=
                $form->field($modelForm, 'id_exp', ['template' => '{label}{hint}{input}', 'hintOptions' => ['class' => 'alert alert-warning']])
                ->hint(Text::_("EXP_TEMPLATING_STYLE_FORM_BIND_APP_HELP"))
                ->element('ui\exp\ExpList', [ 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?> 
        <?= $form->field($modelForm, 'time_active')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>
        <?= $form->field($modelForm, 'time_inactive')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>        
    </div>
    <div class="col-md-6">       
        <?=
                $form->field($modelForm, 'id_menu', ['template' => '{label}{hint}{input}', 'hintOptions' => ['class' => 'alert alert-warning']])
                ->hint(Text::_("EXP_TEMPLATING_STYLE_FORM_BIND_MENU_HELP"))
                ->element('ui\menu\ItemsTree', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]);
        ?>
    </div>
</div>
<?php $tabs->endTab(); ?>

<?php if (isset($params->accordion)): ?>
    <?php $tabs->beginTab(Text::_("EXP_TEMPLATING_STYLE_FORM_TABS_TWO")); ?>
    <?php $acc = JqAccordion::begin(['options' => ['id' => 'admin-accordion-groupmenu']]); ?> 
    <?php foreach ($params->accordion as $fielset): ?>
        <?php $acc->beginTab(Text::_($fielset["title"])); ?>
        <div class="form-horizontal">
            <?php foreach ($fielset as $element): ?>
                <div class="form-group">
                    <?= ui\help\Tooltip::element(['content' => Text::_($element['title']), 'help' => (isset($element['description']) ? $element['description'] : null), 'htmlOptions'=>['class'=>'col-sm-3 control-label']]); ?>
                    <div class="col-sm-9"><?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'param')) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $acc->endTab(); ?>
    <?php endforeach; ?>
    <?php JqAccordion::end(); ?>
    <?php $tabs->endTab(); ?>
<?php endif; ?>

<?php JqTabs::end(); ?>
<?= $form->field($modelForm, 'front', ['template' => '{input}'])->hiddenInput(); ?>
<?php ui\form\FormBuilder::end(); ?>    

<script>
    jQuery().ready(function () {
        $("#style-name").change(function () {
            var $self = $(this);
            if ($self.val() !== '')
            {
                cms.redirect([{'name': $self.val()}]);
            }

        })
    })
</script>