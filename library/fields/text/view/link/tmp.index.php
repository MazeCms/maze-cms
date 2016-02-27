<?php if ($data): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <?= $d->text_value; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <a href="<?php echo \Route::to($param->getRealUrl($data)) ?>"><?= $data->text_value; ?></a>
    <?php endif; ?>
<?php endif; ?>
