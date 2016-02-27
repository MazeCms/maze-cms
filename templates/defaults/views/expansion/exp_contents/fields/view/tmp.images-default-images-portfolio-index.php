<?php

use maze\helpers\Html;
?>

<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <li>
                <a class="mfp-gallery" title="" href="<?= $d->path_image; ?>">
                   <?= Html::imgThumb('@root/' . $d->path_image,800, 330) ?>
                </a>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>
            <a class="mfp-gallery" title="" href="<?= $data->path_image; ?>">
                <?= Html::imgThumb('@root/' . $data->path_image, 800, 330) ?>
            </a>
        </li>
    <?php endif; ?>
<?php else: ?>
    <?php if ($view->field->settings->pathDefault): ?>
        <li>
            <a class="mfp-gallery" title="" href="<?= $view->field->settings->pathDefault; ?>">
                <img src="<?= $view->field->settings->pathDefault; ?>" alt=""/>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>
