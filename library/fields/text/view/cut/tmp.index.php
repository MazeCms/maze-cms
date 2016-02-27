
<?php if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <?= $d->text_value; ?>
    <?php endforeach; ?>
<?php else: ?>
    <?= $data->text_value; ?>
<?php endif; ?>
