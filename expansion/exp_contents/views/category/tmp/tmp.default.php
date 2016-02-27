<?php

use ui\grid\Pagination;
?>
<div class="content-category">
    <?php $model->toolbar->getStart(); ?>
    <?php foreach ($filedView as $view): ?>
        <?= $view->beginWrap; ?>
        <?= $view->renderLabel; ?>
        <?= $view->renderField; ?>
        <?= $view->endWrap; ?>
    <?php endforeach; ?>
    <?= $model->toolbar->run(); ?>
</div>
<?php if (!empty($contentsView)): ?>
    <div class="content-type">
        <?php foreach ($contentsView as $id => $views): ?>
            <div class="row">
                <?php $contentsModel[$id]->toolbar->getStart(); ?>
                <?php foreach ($views as $name => $view): ?>
                    <?= $view->beginWrap; ?>
                    <?= $view->renderLabel; ?>
                    <?= $view->renderField; ?>
                    <?= $view->endWrap; ?>
                <?php endforeach; ?>
                <?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>
                <?= $contentsModel[$id]->toolbar->run(); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ($categoryView): ?>
    <div class="content-type-children">
        <?php foreach ($categoryView as $id => $views): ?>
            <div class="row">
                <?php $categoryModel[$id]->toolbar->getStart(); ?>
                <?php foreach ($views as $name => $view): ?>
                    <?= $view->beginWrap; ?>
                    <?= $view->renderLabel; ?>
                    <?= $view->renderField; ?>
                    <?= $view->endWrap; ?>
                <?php endforeach; ?>
                <?= $categoryModel[$id]->toolbar->run(); ?>
            </div>
            <?php echo Route::to(['/contents/category/category/default', ['term_id' => $id]]); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= Pagination::element(['model' => $paginationModel]) ?>
