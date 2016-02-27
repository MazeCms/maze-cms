<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<!-- Navigation / Left -->
<div class="one carousel column"><div id="showbiz_left_2" class="sb-navigation-left-2"><i class="icon-angle-left"></i></div></div>
<div <?= Html::renderTagAttributes(["class" => "showbiz-container fourteen carousel columns wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class"), "id" => "our-clients"]) ?>>
    <div class="showbiz our-clients" data-left="#showbiz_left_2" data-right="#showbiz_right_2">
        <div class="overflowholder">
            <ul>
                <?php foreach ($contents as $key => $con): $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(),  $con->contents['bundle']) ?>
                    <li>
                        <?php $toolBar->getStart();?>
                        <?php if ($con->viewField): ?>
                            <?php foreach ($con->viewField as $v): ?>
                                <?= $v->beginWrap; ?>
                                <?= $v->renderField; ?>
                                <?= $v->endWrap; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?= $toolBar->run();?>
                    </li>    
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

</div>
<!-- Navigation / Right -->
<div class="one carousel column"><div id="showbiz_right_2" class="sb-navigation-right-2"><i class="icon-angle-right"></i></div></div>