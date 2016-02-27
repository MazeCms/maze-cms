<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>
<div class="wrap-form">
    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'languages-form',
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
            <?= $form->field($modelForm, 'title'); ?>
            <?= $form->field($modelForm, 'lang_code'); ?>
            <?= $form->field($modelForm, 'reduce'); ?>
            <?php
            $list = $model->getIcon(RC::app()->router->exp->config->getVar("img_path"));
            echo $form->field($modelForm, 'img')->element('ui\select\Dropdown', ['items' => $list['items'], 'options' => ['class' => 'form-control', 'options'=>$list['options'],'prompt' => '-- ' . $modelForm->getAttributeLabel('img') . ' --']]); ?>
            <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>  
        </div>
    </div> 
</div>
<?php ui\form\FormBuilder::end(); ?>  