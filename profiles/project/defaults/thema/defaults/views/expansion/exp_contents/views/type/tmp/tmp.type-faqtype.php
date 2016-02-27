<?php

use ui\grid\Pagination;
?>
<h1 class="page-title"><?= $modelType->title; ?></h1>
<div class="content-type-faqtype">
    <?php $isOpen = false; foreach ($views as $id => $filedView): ?>
    
        <div class="toggle-wrap faq">
            <?php $modelByid[$id]->toolbar->getStart(); ?>  
            <?php foreach ($filedView as $name => $view): ?>
                <?php if ($view->field->field_name == 'title'): ?>
                    <span class="trigger<?php if(!$isOpen){$isOpen = true; echo " opened";}?>"><a href="#"><i class="toggle-icon"></i> <?= trim(strip_tags($view->renderField)); ?>?</a></span>
                <?php else: ?>
                    <div class="toggle-container">
                        <?= $view->beginWrap; ?>
                        <?= $view->renderLabel; ?>
                        <?= $view->renderField; ?>
                        <?= $view->endWrap; ?>
                    </div>                        
                <?php endif; ?>
            <?php endforeach; ?> 
            <?= $modelByid[$id]->toolbar->run(); ?>
        </div>          

        
<?php endforeach; ?>
</div>
<?= Pagination::element(['model' => $paginationModel]) ?>
