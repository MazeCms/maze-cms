<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>

<div <?= Html::renderTagAttributes(["class" => "wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class")]) ?>>

    <?php
    $i = 0;
    $y = 0;
    foreach ($contents as $key => $con): $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(), $con->contents['bundle'])
        ?>
        <?php
        if ($i == 0) {
            echo '<div class="row">';
        }
        ?>
        <div class="four columns">
                <?php $toolBar->getStart(); ?>
            <a class="readmore-link" href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $con->getId()]], ['type'=>'services']); ?>">
                <div class="notice-box"> 
                <?php if ($con->viewField): ?>
                    <?php foreach ($con->viewField as $v): ?>
                        <?php if ($v->field->field_name == 'icon'): ?>
                            <i class="<?= trim(strip_tags($v->renderField)); ?>"></i>   
                        <?php else: ?>
                            <?= $v->beginWrap; ?>
                            <?= $v->renderLabel; ?>
                            <?= $v->renderField; ?>
                            <?= $v->endWrap; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
            <?php endif; ?>
                </div>
            </a>
                <?= $toolBar->run(); ?>
        </div>

        <?php
        $i++;
        $y++;
        if ($i == 4 || $y > count($contents)) {
            echo '</div>';
            $i = 0;
        }
        ?>   
<?php endforeach; ?>


</div>
