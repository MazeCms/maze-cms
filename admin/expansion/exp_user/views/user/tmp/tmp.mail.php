<?php
use ui\form\FormBuilder;

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id' => 'mail-form',
            'groupClass' => 'form-group',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e){if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
        ]);
?>
<?= $form->field($modelForm, 'theme');?>

<?= $form->field($modelForm, 'mess')->element('ui\editor\Editor'); ?>
<?php ui\form\FormBuilder::end(); ?>
