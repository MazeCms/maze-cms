<?php

use ui\form\FormBuilder;

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'menu-form-pack',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
        ]);
?>
<?=

$form->field($modelForm, 'id_lang')->element('ui\select\Chosen', ['items' => $modelMenu->listLang(),
    'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]);
?>
<?=

$form->field($modelForm, 'id_tmp')->element('ui\select\Chosen', ['items' => $modelMenu->listTmp(),
    'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_tmp') . ' --']]);
?>
<?=

$form->field($modelForm, 'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control',
        'multiple' => 'multiple'], 'settings' => ['placeholder_text' => '-- ' . $modelForm->getAttributeLabel('id_role') . ' --']]);
?>
<?= $form->field($modelForm,'meta_robots')->element('ui\meta\Robots', ['options' => 
    ['class' => 'form-control', 'style'=>'width:300px; display:block;', 'prompt'=>'-- '.$modelForm->getAttributeLabel('meta_robots').' --']]);
?>
<?= $form->field($modelForm, 'time_active')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]);
?>
<?= $form->field($modelForm, 'time_inactive')->element('ui\date\Datetimepicker', [ 'options' => ['class' => 'form-control']]);
?> 


<?php ui\form\FormBuilder::end(); ?>
