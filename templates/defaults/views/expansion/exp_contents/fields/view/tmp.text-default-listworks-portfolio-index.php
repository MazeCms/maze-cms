
<?php  if (is_array($data)): ?>
    <?php  foreach ($data as $d): ?>
        <li><?= $d->text_value; ?></li>
    <?php endforeach; ?>
<?php else: ?>
        <li><?= $data->text_value; ?></li>
<?php endif; ?>
