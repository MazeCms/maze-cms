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
    <div class="panel-heading">Настройка доступа базы данных</div>
    <div class="panel-body">
        <?php if(!$curentModel->isEmptyDB):?>
            <?= $form->field($curentModel, 'clear', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn')?>
        <?php endif;?>
        <?= $form->field($curentModel, 'host');?>
        <?= $form->field($curentModel, 'encoding');?>
        <?= $form->field($curentModel, 'dbname');?>
        <?= $form->field($curentModel, 'user');?>
        <?= $form->field($curentModel, 'password');?>
        <?= $form->field($curentModel, 'prefix');?>
        <?= $form->field($curentModel, 'type')->element('ui\select\Chosen', ['items'=>['mysql'=>'mysql'], 'model'=>$curentModel, 'attribute'=>'type', 'options'=>['class' => 'form-control']])?>
    </div>
</div>