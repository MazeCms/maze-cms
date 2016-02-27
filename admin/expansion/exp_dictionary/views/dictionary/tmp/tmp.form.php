<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
?>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'dictionary-form',
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
        <div class="col-sm-8 col-md-8">
            <?= $form->field($modelForm, 'title'); ?>
        </div>
        <div class="col-sm-4 col-md-4">
            <?= $form->field($modelForm, 'bundle')->textInput(['disabled' => ($modelForm->scenario !== 'create')]); ?>
        </div>
    </div>
    <?= $form->field($modelForm, 'description')->textarea(); ?>
    <div class="panel panel-default">
        <div class="panel-heading"><?= Text::_("EXP_CONTENTS_LABEL_PARAM") ?></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-8 col-md-8">
                    <?= $form->field($modelParam, 'title'); ?>
                </div>
                <div class="col-sm-4 col-md-4">
                    <?= $form->field($modelParam, 'length'); ?>
                </div>
            </div>
            <?= $form->field($modelParam, 'multilang', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
            <?= $form->field($modelParam, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        </div>
    </div>
    <?php ui\form\FormBuilder::end(); ?>
</div>