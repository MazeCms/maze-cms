<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<div id="team" class="showbiz-container sixteen columns">
    <div class="showbiz-navigation">
        <div id="showbiz_left_4" class="sb-navigation-left"><i class="icon-angle-left"></i></div>
        <div id="showbiz_right_4" class="sb-navigation-right"><i class="icon-angle-right"></i></div>
    </div>
    <div class="clearfix"></div>

    <div <?= Html::renderTagAttributes(["class" => "showbiz  wrapp-view-block  wrapp-view-block-$id " . $params->getVar("css_class"), "id" => $id_css, "data-right" => "#showbiz_right_4", "data-left" => "#showbiz_left_4"]) ?>>
        <div class="overflowholder">
            <ul>
                <?php foreach ($contents as $con): $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(), $con->contents['bundle']) ?>


                    <?php
                    $about = '';
                    $images = '';
                    $name = '';
                    foreach ($con->viewField as $v):
                        ?>
                        <?php
                        switch ($v->field->field_name) {
                            case 'images':
                                $images = trim($v->renderField);
                                break;
                            case 'desc':
                                $about = trim($v->renderField);
                                break;
                            default:
                                $name .= $v->beginWrap;
                                $name .= $v->renderField;
                                $name .= $v->endWrap;
                                break;
                        }
                        ?>
                    <?php endforeach; ?>




                    <li>
                        <?php $toolBar->getStart(); ?>
                        <?= $images ?>
                        <div class="team-name"><?= $name ?></div>
                        <div class="team-about"><p><?= $about ?></p></div>                    
                        <div class="clearfix"></div>
                        <?= $toolBar->run(); ?>
                    </li>


                <?php endforeach; ?> 
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>

</div>