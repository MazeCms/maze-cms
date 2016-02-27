<?php

use maze\helpers\DataTime;
use ui\grid\Pagination;

$bloglayout = RC::app()->theme->param->getVar('bloglayout');
$showdate = RC::app()->theme->param->getVar('showdate');
$showreadmore = RC::app()->theme->param->getVar('showreadmore');
$textreadmore = RC::app()->theme->param->getVar('textreadmore');
?>
<h1 class="page-title"><?= $modelType->title; ?></h1>
<div class="content-type-blog">
    <?php if ($bloglayout == 'two'): ?>
        <?php foreach ($views as $id => $filedView): ?>
            <article class="post medium">
                <?php $modelByid[$id]->toolbar->getStart(); ?>  
                <?php
                $images = '';
                $title = '';
                $des = '';
                foreach ($filedView as $name => $view) {
                    if ($view->field->field_name == 'images') {
                        $view->view->field_view_param = ['width' => 420, 'height' => 292];
                        $images = $view->renderField;
                    } elseif ($view->field->field_name == 'title') {
                        $title = $view->renderField;
                    } else {
                        $des .= $view->beginWrap;
                        $des .= $view->renderLabel;
                        $des .= $view->renderField;
                        $des .= $view->endWrap;
                    }
                }
                ?>              
                <div class="five alt columns alpha">
                    <section class="flexslider post-img">
                        <div class="media">
                            <ul class="slides mediaholder">
                                <?= $images; ?>
                            </ul>                       
                        </div>
                    </section>
                </div>
                <div class="seven columns">
                    <section class="post-content">
                        <header class="meta">
                            <h2><a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>"><?= $title ?></a></h2>
                            <?php if ($showdate && Datehelper::instance()->diffCurrent($modelByid[$id]->contents->date_create)): ?>
                                <ul>
                                    <li><?= Datehelper::instance()->diffCurrent($modelByid[$id]->contents->date_create, 2); ?></li>
                                    <li>Назад</li>
                                </ul>

                            <?php endif; ?>

                        </header>
                        <p><?= $des ?></p>
                        <?php if($showreadmore):?>
                        <a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>" class="button color"><?=$textreadmore?></a>
                        <?php endif; ?>
                    </section>
                </div>
                <div class="clearfix"></div>
                <?= $modelByid[$id]->toolbar->run(); ?>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <?php foreach ($views as $id => $filedView): ?>
            <article class="post">
                <?php $modelByid[$id]->toolbar->getStart(); ?>  
                <?php
                $images = '';
                $title = '';
                $des = '';
                foreach ($filedView as $name => $view) {
                    if ($view->field->field_name == 'images') {
                        $view->view->field_view_param = ['width' => 860, 'height' => 320];
                        $images = $view->renderField;
                    } elseif ($view->field->field_name == 'title') {
                        $title = $view->renderField;
                    } else {
                        $des .= $view->beginWrap;
                        $des .= $view->renderLabel;
                        $des .= $view->renderField;
                        $des .= $view->endWrap;
                    }
                }
                ?>              
                <figure class="post-img media">
                    <section class="flexslider post-img">
                        <div class="media">
                            <ul class="slides mediaholder">
                                <?= $images; ?>
                            </ul>                       
                        </div>
                    </section>
                </figure>
                <div class="post-format">
                    <div class="circle"><i class="icon-pencil"></i><span></span></div>
                </div>

                <section class="post-content">
                    <header class="meta">
                        <h2><a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>"><?= $title ?></a></h2>
                        <?php if ($showdate && Datehelper::instance()->diffCurrent($modelByid[$id]->contents->date_create)): ?>
                            <ul>
                                <li><?= Datehelper::instance()->diffCurrent($modelByid[$id]->contents->date_create, 2); ?></li>
                                <li>Назад</li>
                            </ul>

                        <?php endif; ?>

                    </header>
                    <p><?= $des ?></p>
                    <?php if($showreadmore):?>
                    <a href="<?php echo Route::to(['/contents/controller/contents/default', ['contents_id' => $id]]); ?>" class="button color"><?=$textreadmore?></a>
                    <?php endif; ?>
                </section>

                <div class="clearfix"></div>
                <?= $modelByid[$id]->toolbar->run(); ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= Pagination::element(['model' => $paginationModel]) ?>
