<?php
use maze\helpers\Html;
?>
<div class="wrap-form">
<?php
$form = ui\form\FormBuilder::begin([
            'ajaxSubmit' => true,
            'id'=>'menu-form-menu',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck'=>'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
            'onErrorElem' => ' function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
echo $form->field($modelForm, 'code')->textInput(['disabled' => ($modelForm->scenario !== 'create')]); ;
echo $form->field($modelForm, 'name');
echo $form->field($modelForm, 'description')->textarea();
echo $form->field($modelForm, 'id_group', ['template' => '{input}'])->hiddenInput();
ui\form\FormBuilder::end();
?>
</div>