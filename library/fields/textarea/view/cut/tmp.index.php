
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <?=$param->getCutText($d->text_value); ?>
    <?php endforeach; ?>
<?php else: ?>
    <?=$param->getCutText($data->text_value); ?>
<?php endif; ?>
