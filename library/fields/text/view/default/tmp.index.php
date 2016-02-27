<?php if($data):?>
<?php  if (is_array($data)): ?>
    <?php  foreach ($data as $d): ?>
        <div><?= $d->text_value; ?></div>
    <?php endforeach; ?>
<?php else: ?>
        <?= $data->text_value; ?>
<?php endif; ?>
<?php endif; ?>