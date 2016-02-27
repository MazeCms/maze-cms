<?php

use maze\helpers\Html;
$this->document->setHtmlClass('disable-templates');
?>
<!DOCTYPE HTML>
<html <?= Html::renderTagAttributes(['class' => $this->document->getHtmlClass()]) ?> lang="<?php echo $this->document->get('language') ?>" dir="ltr" xml:lang="<?php echo $this->document->get('language') ?>" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {HEADER}
        <?php
            ui\assets\AssetJquery::register();
            ui\assets\AssetBootstrap::register();
            $this->addStylesheet($this->theme->getUrl("/css/reset.css"), array("sort" => 100));
            $this->addStylesheet($this->theme->getUrl("/css/style.css"), array("sort" => 99));
        ?>
    </head>

    <body <?=Html::renderTagAttributes(['class'=>$this->document->getBodyClass()])?>>
        {TOOLBARPANEL}   
        <?php if ($this->isMessage()): ?>
            {MESSAGE}
        <?php endif; ?>
            <div class="logo-center">
                <img src="<?=$this->theme->getUrl("/images/logo.png")?>">
            </div>
        <?php if (RC::app()->config->get("offline_mess")): ?>
            <div class="message-disable">
                <div class="container">
                    <?php echo RC::app()->config->get("text_offline"); ?>
                </div>
            </div>
        <?php endif; ?>
    </body>
</html>
