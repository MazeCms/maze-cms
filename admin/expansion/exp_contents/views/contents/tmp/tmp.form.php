<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\fields\FieldHelper;
use ui\tabs\JqTabs;
?>

<?php
$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'contents-form',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}'
        ]);
?>
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-tabs-contents']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_CONTENTS_TABS_FIELD")); ?>     
<?php foreach ($model->fields as $field): ?>
    <?php echo FieldHelper::rendertWidget(['field' => $field, 'form' => $form, 'data' => $field->data, 'wrapp'=>'filed-group-'.$field->field_name]); ?>
<?php endforeach; ?>
<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_CONTENTS_TABS_CONT")); ?> 

<?= $form->field($model->routes, 'alias')->element('ui\text\InputAlias', ['options' => ['class' => 'form-control', 'placeholder'=>$model->routes->getAttributeLabel('alias')]]); ?>
<?php if($model->type->param->multilang):?>
<?= $form->field($model->contents,'id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 
    'prompt'=>'-- '.$model->contents->getAttributeLabel('id_lang').' --']]);
?>
<?php endif;?>
<?= $form->field($model,'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 
        'multiple' => 'multiple'], 'settings'=>['placeholder_text'=>'-- '.$model->getAttributeLabel('id_role').' --']]);
    ?>
<div class="row">
    <div class="col-sm-6 col-md-6">
        <?= $form->field($model->contents,'time_active')->element('ui\date\Datetimepicker', ['options' => ['class' => 'form-control']]);?>
    </div>
    <div class="col-sm-6 col-md-6">
        <?= $form->field($model->contents,'time_inactive')->element('ui\date\Datetimepicker', ['options' => ['class' => 'form-control']]);?>
    </div>
</div>
<?= $form->field($model->contents, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
<?= $form->field($model->contents, 'home', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>

<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_CONTENTS_TABS_METACONT")); ?> 
<?= $form->field($model->routes,'meta_robots')->element('ui\meta\Robots', ['options' => 
    ['class' => 'form-control', 'style'=>'width:300px; display:block;', 'prompt'=>'-- '.$model->routes->getAttributeLabel('meta_robots').' --']]);
?>
<?= $form->field($model->routes, 'meta_title');?>
<?= $form->field($model->routes, 'meta_keywords')->textarea();?>
<?= $form->field($model->routes, 'meta_description')->textarea();?>
<?php $tabs->endTab(); ?>
<?php JqTabs::end(); ?>  
<?php ui\form\FormBuilder::end(); ?>
