<div class="category-<?=$bundle?>">
    <?php foreach ($categoryView as $id=>$views): ?>
    <div class="row">
        <?php $modelByid[$id]->toolbar->getStart();?> 
        <?php foreach ($views as $name=>$view): ?>
            <?= $view->beginWrap; ?>
            <?= $view->renderLabel; ?>
            <?= $view->renderField; ?>
            <?= $view->endWrap; ?>
        <?php endforeach; ?>
        <?= $modelByid[$id]->toolbar->run();?>
    </div>
    <?php echo  Route::to(['/contents/category/category/default', ['term_id'=>$id]]);?>
    
    <?php endforeach; ?>
</div>
