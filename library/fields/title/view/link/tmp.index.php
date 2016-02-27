
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <?= $d->title_value; ?>
    <?php endforeach; ?>
<?php else: ?>
<a href="<?= \Route::to($param->getRealUrl($data))?>"><?= $data->title_value; ?></a>
<?php endif; ?>
