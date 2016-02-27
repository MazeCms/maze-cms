<?php

use maze\helpers\Html;
?>
<script>
    jQuery(document).ready(function () {
        var installObj = new mazeInsatll();
        installObj.init()
    })
</script>
<?=Html::errorSummary($curentModel, ['class'=>'alert alert-danger'])?>

<div class="panel panel-default">
    <div class="panel-heading">Настройка учетной записи администратора</div>
    <div class="panel-body">        
        <?= $form->field($curentModel, 'username');?>
        <?= $form->field($curentModel, 'password');?>
        <?= $form->field($curentModel, 'repeat_password');?>
        <?= $form->field($curentModel, 'email');?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">Настройка системы</div>
    <div class="panel-body">        
        <?= $form->field($curentModel, 'site_name');?>
        <?= $form->field($curentModel, 'timezone')->element('ui\date\TimeZone', ['options'=>['class' => 'form-control']])?>
        <?= $form->field($curentModel, 'fromname');?>
        <?= $form->field($curentModel, 'mailfrom');?>
        <?= $form->field($curentModel, 'language')->element('ui\lang\Local'); ?>
    </div>
</div>


