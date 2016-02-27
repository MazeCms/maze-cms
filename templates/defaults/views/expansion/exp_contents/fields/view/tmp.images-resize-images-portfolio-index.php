<?php

use maze\helpers\Html;
?>

<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <a href="<?= $d->path_image ?>" class="mfp-gallery" title="">
                <?= Html::imgThumb('@root/' . $d->path_image, $param->width, $param->height) ?>
                <div class="hovercover">
                    <div class="hovericon"><i class="hoverzoom"></i></div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <a href="<?= $data->path_image ?>" class="mfp-gallery" title="">
            <?= Html::imgThumb('@root/' . $data->path_image, $param->width, $param->height) ?>
            <div class="hovercover">
                <div class="hovericon"><i class="hoverzoom"></i></div>
            </div>
        </a>
    <?php endif; ?>
<?php else: ?>
    <?php if ($view->field->settings->pathDefault): ?>
        <a href="<?= $view->field->settings->pathDefault ?>" class="mfp-gallery" title="">
            <?= Html::imgThumb('@root/' . $view->field->settings->pathDefault, $param->width, $param->height) ?>
            <div class="hovercover">
                <div class="hovericon"><i class="hoverzoom"></i></div>
            </div>
        </a>
    <?php endif; ?>
<?php endif; ?>
