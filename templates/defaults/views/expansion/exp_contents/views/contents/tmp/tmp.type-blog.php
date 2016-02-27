<?php $model->toolbar->getStart(); ?> 

<?php if (isset($ftitleblogp)): ?>
    <?= $ftitleblogp->beginWrap; ?>
    <?= $ftitleblogp->renderLabel; ?>
    <?= $ftitleblogp->renderField; ?>
    <?= $ftitleblogp->endWrap; ?>
<?php endif; ?>
<article class="post" style="margin: 0; border: 0;">
    <section class="flexslider post-img">
        <div class="media">
            <ul class="slides mediaholder">
                <?= $fimagesblog->renderField; ?>
            </ul>                       
        </div>
    </section>
    <div class="post-format">
        <div class="circle"><i class="icon-pencil"></i><span></span></div>
    </div>

    <section class="post-content">
        <?= $fbodyblog->beginWrap; ?>
        <?= $fbodyblog->renderLabel; ?>
        <?= $fbodyblog->renderField; ?>
        <?= $fbodyblog->endWrap; ?>
    </section>
    <div class="clearfix"></div>

</article>
<?= $model->toolbar->run(); ?>