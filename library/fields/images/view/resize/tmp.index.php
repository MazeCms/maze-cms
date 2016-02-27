<?php
use maze\helpers\Html;
?>

<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <span><?=  Html::imgThumb('@root/'.$d->path_image, $param->width, $param->height)?></span>
        <?php endforeach; ?>
    <?php else: ?>
            <?=  Html::imgThumb('@root/'.$data->path_image, $param->width, $param->height)?>
    <?php endif; ?>
<?php else: ?>
        <?php if($view->field->settings->pathDefault):?>
            <?=  Html::imgThumb('@root/'.$view->field->settings->pathDefault, $param->width, $param->height)?>
        <?php endif; ?>
<?php endif; ?>


