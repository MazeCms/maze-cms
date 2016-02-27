<?php

use ui\grid\Pagination;
$colonum = RC::app()->theme->param->getVar('portfoliocol');
?>
<h1 class="page-title"><?= $modelType->title; ?></h1>
<div class="content-type-portfolio">
    <div id="portfolio-wrapper">
        <?php foreach ($views as $id => $filedView): ?>
            <div class="<?=$colonum;?> portfolio-item media">
                <figure>

                    <?php
                    $images = '';
                    $filed = '';
                    foreach ($filedView as $name => $view) {
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
                    <?php $modelByid[$id]->toolbar->getStart(); ?>  
                    <div class="mediaholder">
                        <?= $images ?>
                    </div>
                    <a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>">
                        <figcaption class="item-description">
                            <?= $filed; ?>
                        </figcaption>
                    </a>
                    <?= $modelByid[$id]->toolbar->run(); ?>
                </figure>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?= Pagination::element(['model' => $paginationModel]) ?>
