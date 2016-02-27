<?php
use maze\helpers\Html;
?>
<!DOCTYPE HTML>
<html <?=Html::renderTagAttributes(['class'=>$this->document->getHtmlClass(), 'lang'=>$this->document->get('language'), 'dir'=>'ltr', 'xmlns'=>'http://www.w3.org/1999/xhtml'])?>>
    <head>
        {HEADER}
        <?php
        

        $this->addStylesheet($this->theme->getUrl("/css/reset.css"), array("sort" => 100));
        $this->addStylesheet($this->theme->getUrl("/css/style.css"), array("sort" => 99));

        $this->registerAssetBundle('ui\assets\AssetCore');        
        
        $this->addScript($this->theme->getUrl("/js/templates.js"));
        
        $this->setLangTextScritp(["LIB_FRAMEWORK_VIEW_AJAX_ERROR",
            "LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR",
            "LIB_FRAMEWORK_VIEW_AJAX_REDIRECT",
            "LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE",
            "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "LIB_USERINTERFACE_TOOLBAR_CLOSE",
            "LIB_USERINTERFACE_FIELD_LOGIN",
            "LIB_USERINTERFACE_FIELD_PASS",
            "LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT",
            "LIB_USERINTERFACE_TOOLBAR_PACK_SEND",
            "LIB_FRAMEWORK_VIEW_AJAX_AUTHORIZATION_TITLE",
            "LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TEXT",
            "LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG"
        ]);
        
        ?>
    </head>

    <body <?=Html::renderTagAttributes(['class'=>$this->document->getBodyClass()])?>>
        <div id="wraper">
            <div id="header">
                {WIDGET position="top" wrapper="none"}
            </div>
            <?php if ($this->isWidget("left")): ?>               
                <div class="left-colon">			
                    <div id="left-content">
                        {WIDGET position="left" wrapper="none"}
                    </div>
                </div>
            <?php endif; ?>
            <div class="right-colon">
                <?php if ($this->isWidget("tools")): ?>
                    {WIDGET position="tools" wrapper="none"}
                <?php endif; ?>
                    {WIDGET position="nav" wrapper="none"}
                <div class="wrap-content">{CONTENT}</div>
            </div>
        </div>
    </body>
</html>
