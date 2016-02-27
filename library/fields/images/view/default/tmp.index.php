<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <span><img src="<?= $d->path_image; ?>"></span>
        <?php endforeach; ?>
    <?php else: ?>
        <img src="<?= $data->path_image; ?>">
    <?php endif; ?>
<?php else: ?>
        <?php if($view->field->settings->pathDefault):?>
            <img src="<?= $view->field->settings->pathDefault; ?>">
        <?php endif; ?>
<?php endif; ?>


