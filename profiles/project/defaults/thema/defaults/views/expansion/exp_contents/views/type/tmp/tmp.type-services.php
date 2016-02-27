<?php

use ui\grid\Pagination;
?>
<h1 class="page-title"><?= $modelType->title; ?></h1>
<div class="content-type-services">
    <?php
    $i = 0;
    $y = 0;
    foreach ($views as $id => $filedView):
        ?>
        <?php
        if ($i == 0) {
            echo '<div class="row">';
        }
        ?>
        <div class="four columns">
             <?php $modelByid[$id]->toolbar->getStart(); ?>  
            <a class="readmore-link" href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]], ['type'=>'services']); ?>">
                <div class="notice-box">                   
                    <?php foreach ($filedView as $name => $view): ?>
                    <?php if($view->field->field_name == 'icon'):?>
                      <i class="<?= trim(strip_tags($view->renderField)); ?>"></i>   
                    <?php else:?>
                        <?= $view->beginWrap; ?>
                        <?= $view->renderLabel; ?>
                        <?= $view->renderField; ?>
                        <?= $view->endWrap; ?>
                    <?php endif;?>
                    <?php endforeach; ?>                   
                </div>               
            </a>
             <?= $modelByid[$id]->toolbar->run(); ?>
        </div>
        <?php
        $i++;
        $y++;
        if ($i == 4 || $y > count($views)) {
            echo '</div>';
            $i = 0;
        }
        ?>
<?php endforeach; ?>
</div>
<?= Pagination::element(['model' => $paginationModel]) ?>
