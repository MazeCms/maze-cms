<?php if ($data): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <span><?= $param->getFormatNum($d->number_value); ?></span>
        <?php endforeach; ?>
    <?php else: ?>
        <?= $param->getFormatNum($data->number_value); ?>
    <?php endif; ?>
<?php endif; ?>
