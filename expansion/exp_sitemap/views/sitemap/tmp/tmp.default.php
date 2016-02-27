<?php
use maze\helpers\Html;

$this->addStylesheet(RC::app()->getExpUrl('css/style.css'));
?>
<h1><?=$this->document->get('title')?></h1>
<ul class="sitemap-tree">
<?php foreach ($links['root'] as $link):?>
    <li>
        <?php if($link->enabled):?>
            <?= Html::a($link->title, $link->loc)?>
        <?php else:?>
            <?= $link->title; ?>
        <?php endif;?>
        <?php if(isset($links[$link->loc])):?>
            <?php echo $this->render('default-items', ['links'=>$links, 'parent'=>$link->loc]);?>
        <?php endif;?>
    </li>
<?php endforeach;?>
</ul>