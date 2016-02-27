<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
ui\assets\AssetBootflat::register(['js'=>[]]);
?>

<?php
$form = FormBuilder::begin([
            'id' => 'user-editpass',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
?>
<div class="row">
    <div  class="col-md-4 col-md-offset-4">
        <?= $form->field($modelForm, 'password'); ?>
        <?= $form->field($modelForm, 'repeatpassword'); ?>
        <label class="toggle"><?=  Html::activeCheckbox($modelForm, 'sendemail')?><span class="handle"></span> </label>   <label> <?=$modelForm->getAttributeLabel('sendemail')?> </label>
        <?=Html::submitButton('Отпавить', ['class'=>'btn btn-primary btn-block'])?>
    </div>
 
</div>
<?php ui\form\FormBuilder::end(); ?> 