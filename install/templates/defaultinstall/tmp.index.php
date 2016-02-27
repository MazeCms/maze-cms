<?php

use maze\helpers\Html;
?>
<!DOCTYPE HTML>
<html <?= Html::renderTagAttributes(['class' => $this->document->getHtmlClass()]) ?> dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {HEADER}
        <?php
        ui\assets\AssetBootstrap::register(['js'=>null]);
        $this->addStylesheet($this->theme->getUrl("/css/reset.css"), array("sort" => 100));
        $this->addStylesheet($this->theme->getUrl("/css/style.css"), array("sort" => 99));
       
        $this->registerAssetBundle('ui\assets\AssetCore');
        ui\assets\AssetRaphael::register();
        $this->addScript("/library/jquery/cms/jquery.admin.js", ['sort'=>23]);
        $this->addScript("/library/javascript/logo/logo.js");
        $this->addScript($this->theme->getUrl("/js/templates.js"));
        ?>
    </head>

    <body <?= Html::renderTagAttributes(['class' => $this->document->getBodyClass()]) ?>>
        <div id="wrapper">
            <div id="contents-page">
                <div class="container">
                    {MESSAGE}{CONTENT}
                </div>
            </div>
            <div id="footer-page">
                <div class="container">
                    <div class="row">                        
                        <div class="col-sm-6 col-md-6 col-l">© 2014-2015 «MAZE-CMS» </div>
                        <div class="col-sm-6 col-md-6 col-r"><a href="#">maze-studio.ru|Техподдержка</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="instal-bg"></div>
    </body>
</html>
