
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <a href="<?= \Route::to($param->getRealUrl($d))?>"><?= $d->termTitle; ?></a>
    <?php endforeach; ?>
<?php else: ?>
<a href="<?= \Route::to($param->getRealUrl($data))?>"><?= $data->termTitle; ?></a>
<?php endif; ?>
