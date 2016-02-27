<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>
<script>
    jQuery(document).ready(function () {
        if ($('#user-newpass').is('#user-newpass'))
        {
            $('.pass-group').hide();
        }

        $('#user-newpass').bind('unChecked.mazeSwitch checked.mazeSwitch', function (e) {
            $('.pass-group')[(e.type == 'checked' ? 'show' : 'hide')]();
        })
    })
</script>
<div class="wrap-form">    
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'user-form',
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
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-6">            
            <?= $form->field($modelForm, 'avatar')->element('ui\images\AddImage', ['settings' => ['max_img' => 1]]); ?>            
            </div>
            <div class="col-md-6">
                <?= $form->field($modelForm, 'email'); ?>
                <?= $form->field($modelForm, 'username'); ?>
<?= $form->field($modelForm, 'name'); ?>
            </div>

            <div  class="col-md-12 pass-group">
                <?= $form->field($modelForm, 'new_password'); ?>
                <?= $form->field($modelForm, 'repeat_password'); ?>
<?= $form->field($modelForm, 'send_email', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            </div>
            <div  class="col-md-12">
                <?php if ($modelForm->scenario !== 'create'): ?>
                    <?= $form->field($modelForm, 'newpass', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
                <?php endif; ?>
                <?= $form->field($modelForm, 'bloc', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            </div>

        </div>
        <div class="col-md-6">
            <?php if($this->access->roles("user", "VIEW_ROLE")):?>
            <?= $form->field($modelForm, 'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 'multiple' => true]]); ?>
            <?php endif;?>
            <?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]); ?>
            <?= $form->field($modelForm, 'timezone')->element('ui\date\TimeZone', ['options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('timezone') . ' --']]); ?>
            <?= $form->field($modelForm, 'editor_site')->element('ui\editor\Lists', ['front'=>1, 'options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_site') . ' --']]); ?>
            <?= $form->field($modelForm, 'editor_admin')->element('ui\editor\Lists', ['front'=>0, 'options' => ['class' => 'form-control', 
                'prompt' => '-- ' . $modelForm->getAttributeLabel('editor_admin') . ' --']]); ?>
        </div>
    </div>
<?php ui\form\FormBuilder::end(); ?>    
</div>
