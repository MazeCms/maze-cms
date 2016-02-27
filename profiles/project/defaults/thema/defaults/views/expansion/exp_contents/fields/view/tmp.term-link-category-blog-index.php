
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <a href="<?= \Route::to($param->getRealUrl($d))?>"><i class="fa fa-link"></i> <?= $d->termTitle; ?></a>  
    <?php endforeach; ?>
<?php else: ?>
<a href="<?= \Route::to($param->getRealUrl($data))?>"><i class="fa fa-link"></i> <?= $data->termTitle; ?></a>
<?php endif; ?>
