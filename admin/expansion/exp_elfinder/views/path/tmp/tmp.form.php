<?php

use ui\form\FormBuilder;
use maze\helpers\Html;
?>


<div class="wrap-form">    
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'elfinder-path-form',
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
    <?= $form->field($modelForm, 'path', ['hintOptions'=>['class'=>'alert alert-warning']])
            ->hint(Text::_('EXP_ELFINDER_DIR_PATH_HELP')); ?>
    <?= $form->field($modelForm, 'alias'); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelForm, 'uploadMaxSize', ['hintOptions'=>['class'=>'alert alert-warning']])
            ->hint(Text::_('EXP_ELFINDER_DIR_UPLOADMAXSIZE_HELP'));; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($modelForm, 'acceptedName'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($modelForm, 'uploadallow')->element('ui\type\MimeList', ['options' => ['class' => 'form-control', 'multiple' => 'multiple']]); ?>
        </div>
    </div>
    <div class="elfinder-path-attributes form-group">
        <label><?=Text::_('EXP_ELFINDER_DIR_ATTR')?></label>
    <?php foreach ($attributes as $key => $attr): ?>
        <div class="input-group form-group">
            <span class="input-group-btn">
                <button class="btn btn-danger" onclick="return deleteElfinderAttribuntes(this)" type="button"><span aria-hidden="true" class="glyphicon glyphicon-trash"></span></button>
            </span>
            <?= Html::activeTextInput($attr, "[$key]pattern", ['class' => 'form-control']); ?>
            <span class="input-group-btn elfinder-checkbox">
                <button type="button" class="btn btn-default">
                    <?= Html::activeCheckbox($attr, "[$key]read", ['class' => 'hide']) ?><?=Text::_('EXP_ELFINDER_READ')?>
                </button>
                <button type="button" class="btn btn-default">
                    <?= Html::activeCheckbox($attr, "[$key]write", ['class' => 'hide']) ?><?=Text::_('EXP_ELFINDER_WRITE')?>
                </button>
                <button type="button" class="btn btn-default">
                    <?= Html::activeCheckbox($attr, "[$key]hidden", ['class' => 'hide']) ?><?=Text::_('EXP_ELFINDER_HIDDEN')?>
                </button>
                <button type="button" class="btn btn-default">
                    <?= Html::activeCheckbox($attr, "[$key]locked", ['class' => 'hide']) ?><?=Text::_('EXP_ELFINDER_LOCKED')?>
                </button>
            </span>                    
        </div>
    <?php endforeach; ?>
    </div>
    <div class="form-group">
        <button class="btn btn-primary btn-block" onclick="return addElfinderAttribuntes()" type="button"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> <?=Text::_('EXP_ELFINDER_ADD')?></button>
    </div>
    
    <?php FormBuilder::end(); ?>  
</div>


<script>
function addElfinderAttribuntes(){
     var attr = $('.elfinder-path-attributes').children('.input-group').eq(0).clone();
     attr.find('input[type=text]').val('');
     attr.find('input[type=checkbox]').removeAttr('checked');
     attr.find('.active').removeClass('active')
     $('.elfinder-path-attributes').append(attr);
     attr.find('.elfinder-checkbox').removeAttr('data-checkbox')
     checkBoxElfinderInit();
     reIndexElfinderAttribuntes();
     return false;
}
function checkBoxElfinderInit(){
    $('.elfinder-checkbox').not('[data-checkbox]').each(function(){
        $(this).attr('data-checkbox', 1);
        $(this).find('button').each(function(){
            if($(this).find('input[type=checkbox]').is(':checked')){
                $(this).addClass('active')
            }
            
            $(this).click(function(){
                $(this).toggleClass('active');
                var check = $(this).find('input[type=checkbox]');
                if(check.is(':checked')){
                    check.removeAttr('checked')
                }else{
                    check.attr('checked', 'checked')
                }
            })
        })
    })
}
function deleteElfinderAttribuntes(elem){
    if($('.elfinder-path-attributes').children('.input-group').size() ==1){
        return false;
    }
    $(elem).closest('.input-group').remove();
}

function reIndexElfinderAttribuntes(){
    $('.elfinder-path-attributes .input-group').each(function(i){
        $(this).find('input[type=text]').attr('name', 'Attributes['+i+'][pattern]');
        $(this).find('input[type=checkbox], input[type=hidden]').each(function(){
            var name = $(this).attr('name');
            var nameSub = name.match(/Attributes\[\d+\]\[([^\]]+)\]/)
            $(this).attr('name', 'Attributes['+i+']['+nameSub[1]+']')
        });
         $(this).find('input[type=text], input[type=checkbox], input[type=hidden]').removeAttr('id')
    })
}
    
jQuery().ready(function() {
    checkBoxElfinderInit();

});
</script>