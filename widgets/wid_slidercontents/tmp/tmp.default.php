<?php

use maze\helpers\Html;

wid\wid_slidercontents\assets\AssetCarusel::register();
$id_css =  ($params->getVar("css_id") ? $params->getVar('css_id') : "slidercontents-$id");
?>
<div <?= Html::renderTagAttributes(["class" => "slidercontents-wrapp owl-carousel owl-theme " . $params->getVar("css_class"), "id" =>$id_css]) ?>>
    <?php foreach ($contents as $key => $con): ?>
        <div class="item item-<?= $key + 1 ?>">
            <div class="content-center">
                <?php if ($con->viewField): ?>
                    <?php foreach ($con->viewField as $v): ?>
                        <?= $v->beginWrap; ?>
                        <?= $v->renderField; ?>
                        <?= $v->endWrap; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div> 
        </div>    
    <?php endforeach; ?>

</div>
<script>
    jQuery().ready(function () {
        $('#<?=$id_css?>').owlCarousel({
            slideSpeed: 500,
            paginationSpeed: 500,
            addClassActive: 'active-slide',
            singleItem: true,
        });
    })
</script>

