<?php

use maze\helpers\Html;
?>

<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <li> <a class="mfp-gallery"  href="<?= $d->path_image ?>" title="">
                    <?= Html::imgThumb('@root/' . $d->path_image, $param->width, $param->height) ?>
                    <div class="hovercover">
                        <div class="hovericon"><i class="hoverzoom"></i></div>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>
            <a class="mfp-gallery"  href="<?= $data->path_image ?>" title="">
                <?= Html::imgThumb('@root/' . $data->path_image, $param->width, $param->height) ?>
                <div class="hovercover">
                    <div class="hovericon"><i class="hoverzoom"></i></div>
                </div>
            </a>
        </li>
    <?php endif; ?>
<?php else: ?>
    <?php if ($view->field->settings->pathDefault): ?>
        <li>
            <a class="mfp-gallery"  href="<?= $view->field->settings->pathDefault ?>" title="">
                <?= Html::imgThumb('@root/' . $view->field->settings->pathDefault, $param->width, $param->height) ?>
                <div class="hovercover">
                    <div class="hovericon"><i class="hoverzoom"></i></div>
                </div>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>
