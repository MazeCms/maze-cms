
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <?= $d->title_value; ?>
    <?php endforeach; ?>
<?php else: ?>
    <?= $data->title_value; ?>
<?php endif; ?>
