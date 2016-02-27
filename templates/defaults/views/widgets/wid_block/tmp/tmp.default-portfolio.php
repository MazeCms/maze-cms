<?php

use maze\helpers\Html;
use tmp\defaults\helpers\ToolBare;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<div id="recent-work" class="showbiz-container sixteen columns">
    <div class="showbiz-navigation">
        <div id="showbiz_left_1" class="sb-navigation-left"><i class="icon-angle-left"></i></div>
        <div id="showbiz_right_1" class="sb-navigation-right"><i class="icon-angle-right"></i></div>
    </div>
    <div class="clearfix"></div>
    <div <?= Html::renderTagAttributes(["class" => "showbiz wrapp-view-block wrapp-view-block-$id " . $params->getVar("css_class"), "id" => $id_css, 'data-left' => '#showbiz_left_1', 'data-right' => '#showbiz_right_1']) ?>>
        <div class="overflowholder">
            <ul>
                <?php foreach ($contents as $con):  $toolBar = ToolBare::getToolbarContents($con->getId(), $con->getTitle(),  $con->contents['bundle']) ?>
                    <li>
                        <?php $toolBar->getStart();?>
                        <div class="portfolio-item media">
                            <figure>
                                <?php
                                $field = '';
                                $images = '';
                                foreach ($con->viewField as $v):
                                    ?>
                                    <?php
                                    if ($v->field->field_name == 'images') {
                                        $images = $v->renderField;
                                    } else {
                                        $field .= $v->beginWrap;
                                        $field .= $v->renderField;
                                        $field .= $v->endWrap;
                                    }
                                    ?>
                                <?php endforeach; ?>
                                <div class="mediaholder">                                
                                    <?php echo $images; ?>
                                </div>

                                <a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $con->getId()]]); ?>">
                                    <figcaption class="item-description">
                                        <?= $field ?>
                                    </figcaption>
                                </a>

                            </figure>
                        </div>
                        <?= $toolBar->run();?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>