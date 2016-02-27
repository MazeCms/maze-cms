<?php

use maze\helpers\Html;

$form = ui\form\FormBuilder::begin([
           'ajaxSubmit' => true,
            'id' => 'admin-gadget-form',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
echo $form->field($modelForm, 'title');
?>

<?php
/*
 * Параметры настройки
 */
?>   
<?php if (isset($gadget->param)): ?>
<div class="form-horizontal">
    <?php foreach ($gadget->param->getParams()->element as $elem): ?>
        <div class="form-group">
            <?php echo ui\help\Tooltip::element(['help'=>(isset($elem["description"]) ? Text::_($elem["description"]) : ''), 'content'=> Text::_($elem['title']), 'htmlOptions'=>['class'=>'col-sm-5 control-label']]); ?>          
            <div class="col-sm-7"><?php echo $gadget->param->elemenet($elem, 'FormSetiingsGadget[param]') ?></div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php ui\form\FormBuilder::end(); ?>
