<?php
use maze\helpers\Html;


?>
<ul>
<?php foreach ($links[$parent] as $link):?>
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