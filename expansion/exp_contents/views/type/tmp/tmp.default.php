<?php
use ui\grid\Pagination;
?>
<div class="content-type">
    <?php foreach ($views as $id=>$filedView): ?>
    <?php $modelByid[$id]->toolbar->getStart();?>  
        <?php foreach ($filedView as $name=>$view): ?>
            <?= $view->beginWrap; ?>
            <?= $view->renderLabel; ?>
            <?= $view->renderField; ?>
            <?= $view->endWrap; ?>
        <?php endforeach; ?>
     <?php echo  Route::to(['/contents/controller/contents/default', ['contents_id'=>$id]]);?>
    <?= $modelByid[$id]->toolbar->run();?>
    <?php endforeach; ?>
</div>
<?=Pagination::element(['model'=>$paginationModel])?>
