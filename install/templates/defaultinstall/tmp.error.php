<!DOCTYPE HTML>
<html lang="<?php echo $this->document->get('language') ?>" dir="ltr"  xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {HEADER}
        <?php


        $this->addStylesheet($this->theme->getUrl("css/reset.css"), array("sort" => 100));
        $this->addStylesheet($this->theme->getUrl("css/style.css"), array("sort" => 99));


        //$this->addScript($this->theme->getUrl("js/templates.js"));
 
        ?>
        
    </head>

    <body>
        <style>
            .error{
                font-size: 80px;
                text-align:center;
            }
        </style>
        <div id="wraper">
            <div class="wrap-content error">Ошибка 404</div>
        </div>
    </body>
</html>



