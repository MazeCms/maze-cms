<div class="gadgets-list">
    <div class="list-group">
        <?php foreach ($gadgets as $gad): ?>
            <a class="list-group-item" href="<?= Route::_([['run' => 'addGadget', 'name' => $gad->sys_name, 'id_des'=>$id_des ]]); ?>">
                <h4 class="list-group-item-heading"><?= $gad->name; ?> - [<?=$gad->sys_name?>]</h4>
                <p class="list-group-item-text"><?php echo $gad->description ?></p>
            </a>
        <?php endforeach; ?>  
    </div>
</div>
