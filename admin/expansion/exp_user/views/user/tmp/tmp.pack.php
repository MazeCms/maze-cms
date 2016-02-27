<div style="width: 500px;">
<?php
use ui\form\FormBuilder;

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'pack-form',
            'groupClass' => 'pack-form',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e){if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
        ]);
?>
<?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => true]]); ?>
<?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]); ?>
<?= $form->field($modelForm, 'timezone')->element('ui\date\TimeZone', ['options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('timezone') . ' --']]); ?>
<?= $form->field($modelForm, 'editor_site')->element('ui\editor\Lists', ['front'=>1, 'options' => ['class' => 'form-control', 
    'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_site') . ' --']]); ?>
<?= $form->field($modelForm, 'editor_admin')->element('ui\editor\Lists', ['front'=>0, 'options' => ['class' => 'form-control', 
    'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_admin') . ' --']]); ?>

<?php ui\form\FormBuilder::end(); ?>
</div>