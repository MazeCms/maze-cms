<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
?>
<?php

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'widget-form-pack',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
        ]);
?>
<?= $form->field($modelForm, 'title_show', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
<?= $form->field($modelForm, 'enable_cache', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
<?= $form->field($modelForm, 'time_cache')->element('ui\text\InputSpinner', [ 'settings' => ['min' => 1, 'max' => 1000, 'step' => 1]]); ?>
<?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', [ 'options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
<?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', [ 'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]); ?>
<?= $form->field($modelForm, 'time_active')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>
<?= $form->field($modelForm, 'time_inactive')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]); ?>      
<?php ui\form\FormBuilder::end(); ?> 
