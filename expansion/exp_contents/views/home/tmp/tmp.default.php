<?php
use ui\grid\Pagination;

?>
<div class="row"> 
<?php foreach ($views as $id=>$filedView): ?>
   
    <div class="col-sm-6">
    <?php $modelByid[$id]->toolbar->getStart();?>  
    <?php foreach ($filedView as $view): ?>
        <?= $view->beginWrap; ?>
        <?= $view->renderLabel; ?>
        <?= $view->renderField; ?>
        <?= $view->endWrap; ?>
        
    <?php endforeach; ?>
     <?php echo  Route::to(['/contents/controller/contents/default', ['contents_id'=>$id]]);?>   
     <?= $modelByid[$id]->toolbar->run();?>
    
   
    </div>
<?php endforeach; ?>
</div>
<?=Pagination::element(['model'=>$paginationModel])?>


