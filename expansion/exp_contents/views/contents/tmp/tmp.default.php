<?php foreach($filedView as $view):?>
<?=$view->beginWrap;?>
<?=$view->renderLabel;?>
<?=$view->renderField;?>
<?= $view->endWrap;?>
<?php endforeach; ?>
