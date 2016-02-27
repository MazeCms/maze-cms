<?php

use maze\helpers\Html;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<div <?= Html::renderTagAttributes(["class" => "wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class"), "id" => $id_css]) ?>>
    <?php foreach ($contents as $key => $con): ?>
        <div class="view-block-item">
            <?php if ($con->viewField): ?>
                <?php foreach ($con->viewField as $v): ?>
                    <?= $v->beginWrap; ?>
                    <?= $v->renderLabel; ?>
                    <?= $v->renderField; ?>
                    <?= $v->endWrap; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>    
    <?php endforeach; ?>
</div>

