<?php

use maze\helpers\Html;

$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "view-block-$id");
?>
<div <?= Html::renderTagAttributes(["class" => "fullwidthbanner-container wrapp-view-block  wrapp-view-block-$id " . $params->getVar("css_class"), "id" => $id_css]) ?>>
    <div class="fullwidthbanner">
        <ul>
            <?php foreach ($contents as $con): ?>


                <?php
                $body = '';
                $timespeed = '';
                $images = '';
                foreach ($con->viewField as $v):
                    ?>
                    <?php
                    switch ($v->field->field_name) {
                        case 'timespeed':
                            $timespeed = trim($v->renderField);
                            break;
                        case 'images':
                            $images = $v->renderField;
                            break;
                        default:
                            $body = $v->beginWrap;
                            $body .= $v->renderField;
                            $body .= $v->endWrap;
                            break;
                    }
                    ?>
                <?php endforeach; ?>
                <li data-fstransition="fade" data-transition="fade" data-slotamount="10" data-masterspeed="<?= $timespeed ?>">
                    <?= $images ?>
                    <?= $body ?>
                </li> 
            <?php endforeach; ?>

        </ul>
    </div>
</div>

