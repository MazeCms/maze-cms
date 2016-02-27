
<?php  if (is_array($data)): ?>
    <?php foreach ($data as $d): ?>
        <div><?php  echo $d->termTitle; ?></div>
    <?php endforeach; ?>
<?php else: ?>
        <?php echo $data->termTitle;?>
<?php  endif; ?>
