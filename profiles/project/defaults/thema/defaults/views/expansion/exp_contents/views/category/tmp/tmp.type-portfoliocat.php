<?php

use ui\grid\Pagination;

$colonum = RC::app()->theme->param->getVar('portfoliocol');
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
    <div class="content-type-portfolio">
        <div id="portfolio-wrapper">
    <?php foreach ($contentsView as $id => $views): ?>
                <div class="<?= $colonum; ?> portfolio-item media">
                    <figure>
                        <?php
                        $images = '';
                        $filed = '';
                        foreach ($views as $name => $view) {
                            if ($view->field->field_name == 'images') {
                                $images = $view->renderField;
                                ;
                            } else {
                                $filed .= $view->beginWrap;
                                $filed .= $view->renderLabel;
                                $filed .= $view->renderField;
                                $filed .= $view->endWrap;
                            }
                        }
                        ?>
                        <?php $contentsModel[$id]->toolbar->getStart(); ?>
                        <div class="mediaholder">
                            <?= $images ?>
                        </div>
                        <a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>">
                            <figcaption class="item-description">
                            <?= $filed; ?>
                            </figcaption>
                        </a>
                        <?= $contentsModel[$id]->toolbar->run(); ?>
                    </figure>
                </div>
    <?php endforeach; ?>
        </div>      
    </div>
<?php endif; ?>

<?= Pagination::element(['model' => $paginationModel]) ?>