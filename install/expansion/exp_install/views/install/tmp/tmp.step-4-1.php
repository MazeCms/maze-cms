<?php

use maze\helpers\Html;

$stepElem = $wizardInstall->getStepElement($wizardStep);
?>
<script>
    jQuery(document).ready(function () {
        var installObj = new mazeInsatll();
        installObj.init();
    })
</script>
<?= Html::errorSummary($wizardInstall->getStepModel($wizardStep), ['class' => 'alert alert-danger']) ?>
<div>
    <?php foreach ($stepElem as $elem): ?>

        <div class="form-group clearfix">
            <?= ui\help\Tooltip::element(['content' => Text::_($elem['title']), 'help' => (isset($elem['description']) ? Text::_($elem['description']) : null), 'htmlOptions' => ['class' => 'control-label']]); ?>
            <?php echo $wizardInstall->config->elemenet($elem, $wizardInstall->getStepModel($wizardStep)); ?>
        </div>        

    <?php endforeach; ?>
</div>
<?php echo Html::activeHiddenInput($curentModel, 'name'); ?> 