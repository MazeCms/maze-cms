<div class="widget-list">
    <div class="list-group">
        <?php foreach ($widgets as $wid): ?>
            <a class="list-group-item" href="<?= Route::_([['run' => 'add', 'name' => $wid['name'], 'front' => $wid['front']]]); ?>">
                <h4 class="list-group-item-heading"><?= $wid['title'] ?> [<?= $wid['name'] ?>]</h4>
                <p class="list-group-item-text"><?= $wid['description'] ?></p>
            </a>
        <?php endforeach; ?>  
    </div>
</div>