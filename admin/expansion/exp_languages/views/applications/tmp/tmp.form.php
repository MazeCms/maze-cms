<?php

use maze\helpers\Html;
use ui\form\FormBuilder;

?>
<script>
    jQuery().ready(function(){
        function updateApp(html){
             $('#formapp-id_app').find('option').filter(function(){return $(this).val() !== ''}).remove();
            $('#formapp-id_app').append(html).trigger("chosen:updated");
        }
        $('#formapp-front, #formapp-type').change(function(){
            var html = '';
            if($('#formapp-front').val() !== '' && $('#formapp-type').val() !== ''){
                $.get(cms.getURL([{run:'appname', clear:'ajax'}]), 
                {type:$('#formapp-type').val(), front:$('#formapp-front').val()}, function(data){
                    
                    updateApp(data)
                })
            }else{
                updateApp()
            }
           
            
        })
    })    
</script>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'languages-app-form',
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


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelForm, 'front')->element('ui\select\Chosen', ['items' => [Text::_("EXP_LANGUAGES_APP_TABLE_ADMIN"), Text::_("EXP_LANGUAGES_APP_TABLE_SITE")],
            'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('front') . ' --']]); ?>
            <?= $form->field($modelForm, 'type')->element('ui\select\Chosen', ['items' => $model->getTypeApp(),
            'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('type') . ' --']]); ?>            
            <?= $form->field($modelForm, 'defaults', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelForm, 'id_app')->element('ui\select\Chosen', ['items' => [],
            'options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_app') . ' --']]); ?>
            <?= $form->field($modelForm, 'id_lang')->element('ui\lang\Langs', ['options' => ['class' => 'form-control', 'prompt' => '-- ' . $modelForm->getAttributeLabel('id_lang') . ' --']]);?>
        </div>
        
    </div> 
</div>
<?php ui\form\FormBuilder::end(); ?>  