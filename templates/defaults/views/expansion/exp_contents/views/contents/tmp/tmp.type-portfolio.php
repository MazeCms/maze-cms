<?php

$colonum = RC::app()->theme->param->getVar('portfoliopage');
?>
<?php $model->toolbar->getStart(); ?> 

<?php if (isset($ftitleportf)): ?>

    <?= $ftitleportf->beginWrap; ?>
    <?= $ftitleportf->renderLabel; ?>
    <?= $ftitleportf->renderField; ?>
    <?= $ftitleportf->endWrap; ?>

<?php endif; ?>
<?php if (isset($fimagespor)): ?>
    <!-- Slider -->
    <div class="<?= $colonum == 'wide' ? 'clearfix' : 'eleven alt columns'?>">
        <!-- FlexSlider  -->
        <section class="flexslider post-img">
            <div class="media">
                <ul class="slides mediaholder">
                    <?= $fimagespor->beginWrap; ?>
                    <?= $fimagespor->renderLabel; ?>
                    <?= $fimagespor->renderField; ?>
                    <?= $fimagespor->endWrap; ?>
                </ul>
            </div>
        </section>
    </div>
    <!-- Slider / End -->
<?php endif; ?>
<div class="<?= $colonum == 'wide' ? 'clearfix' : 'five columns'?>">
    <?php if (isset($fbodyport)): ?>
        <!-- Job Description -->
        <div <?= $colonum == 'wide' ? 'class="twelve columns"' : 'class="widget" style="margin: 5px 0 0 0;"'?>>
            <h3 class="headline"><?= $fbodyport->renderLabel; ?></h3><span class="line"></span><div class="clearfix"></div>
            <p><?= $fbodyport->renderField; ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($flistworkspot)): ?>
        <!-- Job Description -->
        <div <?= $colonum == 'wide' ? 'class="four columns"' : 'class="widget" style="margin: 5px 0 0 0;"'?>>
            <h3 class="headline" ><?= $flistworkspot->renderLabel; ?></h3><span class="line"></span><div class="clearfix"></div>

            <ul class="list-3" style="margin: 5px 0 18px 0;">
                 <?= $flistworkspot->renderField; ?>
            </ul>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
</div>

<?= $model->toolbar->run(); ?>