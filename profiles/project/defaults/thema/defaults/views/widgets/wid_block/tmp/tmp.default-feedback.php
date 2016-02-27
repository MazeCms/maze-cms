<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>

<!-- Navigation / Left -->
<div id="showbiz_left_3" class="sb-navigation-left-2 alt"><i class="icon-angle-left"></i></div>

<div <?= Html::renderTagAttributes(["class" => "showbiz-container sixteen carousel columns wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class"), "id" => "happy-clients"]) ?>>
    <div class="showbiz our-clients" data-left="#showbiz_left_3" data-right="#showbiz_right_3">
        <div class="overflowholder">
            <ul>
                <?php foreach ($contents as $key => $con): $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(), $con->contents['bundle']) ?>


                    <?php if ($con->viewField): $flag = true;
                        $content = ''; ?>
                        <?php foreach ($con->viewField as $v): ?>
                            <?php
                            if ($v->field->field_name == 'username' && empty(trim($v->renderField))) {
                                $flag = false;
                                break;
                            }
                            ?>
                            <?php $content .= $v->beginWrap; ?>
                            <?php $content .= $v->renderField; ?>
                            <?php $content .= $v->endWrap; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($flag): ?>
                        <li>
                        <?php $toolBar->getStart(); ?>
                            <?=$content;?>
                        <?= $toolBar->run(); ?>
                        </li>
                    <?php endif; ?>


            <?php endforeach; ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

</div>
<!-- Navigation / Right -->
<div id="showbiz_right_3" class="sb-navigation-right-2 alt"><i class="icon-angle-right"></i></div>