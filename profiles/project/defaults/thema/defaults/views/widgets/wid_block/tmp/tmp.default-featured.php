<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>

<div <?= Html::renderTagAttributes(["class" => "featured-boxes homepage wrapp-view-block  wrapp-view-block-$id " . $params->getVar("css_class"), "id" => $id_css]) ?>>

    <?php
    $i = 0;
    $c = 0;
    foreach ($contents as $con): $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(),  $con->contents['bundle']) 
        ?>


        <?php
        $field = '';
        $icon = '';
        foreach ($con->viewField as $v):
            ?>
            <?php
            switch ($v->field->field_name) {
                case 'icon':
                    $icon = trim($v->renderField);
                    break;
                default:
                    $field .= $v->beginWrap;
                    $field .= $v->renderField;
                    $field .= $v->endWrap;
                    break;
            }
            ?>
        <?php endforeach; ?>
        <?php
        if ($i == 0) {
            echo '<div class="row">';
        }
        ?>
        <div class="one-third column">
            <?php $toolBar->getStart();?>
            <div class="featured-box">
                <div class="circle"><i class="<?= $icon ?>"></i><span></span></div>
                <div class="featured-desc">
                <?= $field ?>
                </div>
            </div>
            <?= $toolBar->run();?>
        </div>
        <?php
        $i++;
        $c++;
        if ($i == 3 || $c == count($contents)) {
            echo '</div>';
            $i = 0;
        }
        ?>
<?php endforeach; ?>

</div>

