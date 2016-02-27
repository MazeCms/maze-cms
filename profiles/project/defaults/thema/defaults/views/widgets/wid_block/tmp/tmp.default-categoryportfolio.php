<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<!-- Navigation / Left -->

<div <?= Html::renderTagAttributes(["class" => "headline wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class"), "id" => "filters"]) ?>>

    <ul class="option-set" data-option-key="filter">
        <?php foreach ($contents as $key => $con): ?>
            <li>
                <a href="<?php echo Route::to(['/contents/category/category/default', ['term_id' => $con->getId()]]); ?>">
                    <?php if ($con->viewField): ?>
                        <?php foreach ($con->viewField as $v): ?>
                            <?= $v->beginWrap; ?>
                            <?= $v->renderField; ?>
                            <?= $v->endWrap; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </a>
            </li>    
        <?php endforeach; ?>
    </ul>
</div>