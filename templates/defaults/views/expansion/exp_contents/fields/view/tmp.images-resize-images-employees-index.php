<?php

use maze\helpers\Html;
?>

<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <?= Html::imgThumb('@root/' . $d->path_image, $param->width, $param->height, ['class'=>'mediaholder team-img']) ?>
        <?php endforeach; ?>
    <?php else: ?>
         <?= Html::imgThumb('@root/' . $data->path_image, $param->width, $param->height, ['class'=>'mediaholder team-img']) ?>
    <?php endif; ?>
<?php else: ?>
    <?php if ($view->field->settings->pathDefault): ?>
        <?= Html::imgThumb('@root/' . $view->field->settings->pathDefault, $param->width, $param->height, ['class'=>'mediaholder team-img']) ?>
    <?php endif; ?>
<?php endif; ?>
