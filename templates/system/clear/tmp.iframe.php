<?php

use maze\helpers\Html;
?>
<!DOCTYPE HTML>
<html <?= Html::renderTagAttributes(['class' => $this->document->getHtmlClass()]) ?> lang="<?php echo $this->document->get('language') ?>" dir="ltr" xml:lang="<?php echo $this->document->get('language') ?>" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {HEADER}
        <?php
            ui\assets\AssetJquery::register();
            ui\assets\AssetBootstrap::register();
        ?>
    </head>

    <body <?=Html::renderTagAttributes(['class'=>$this->document->getBodyClass()])?>>
        {TOOLBARPANEL}   
        <?php if ($this->isMessage()): ?>
            {MESSAGE}
        <?php endif; ?>
       {CONTENT}
    </body>
</html>