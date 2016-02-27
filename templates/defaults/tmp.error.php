<?php

use maze\helpers\Html;

$this->document->setBobyClass($this->theme->param->getVar('layoutstyle'));
$this->document->setBobyClass('theme-defaults');

$logo = $this->theme->param->getVar('logo');
$favicon = $this->theme->param->getVar('favicon');
$bg = $this->theme->param->getVar('bg');
if ($favicon && file_exists(RC::getAlias('@root/' . $favicon))) {
    $this->document->set("favicon", $favicon);
}
?>
<!DOCTYPE HTML>
<html <?= Html::renderTagAttributes(['class' => $this->document->getHtmlClass()]) ?> lang="<?php echo $this->document->get('language') ?>" dir="ltr" xml:lang="<?php echo $this->document->get('language') ?>" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
        {HEADER}
        <?php
        ui\assets\AssetJquery::register();
        ui\assets\AssetFontAwesome::register();
        
        $this->addStylesheet($this->theme->getUrl("/css/base.css"), array("sort" => 100));
        $this->addStylesheet($this->theme->getUrl("/css/responsive.css"), array("sort" => 99));
        //$this->addStylesheet($this->theme->getUrl("/css/icons.css"), array("sort" => 98));
        $this->addStylesheet($this->theme->getUrl("/css/style.css"), array("sort" => 97));
        $this->addStylesheet($this->theme->getUrl("/css/colors/".$this->theme->param->getVar('stylecolor').".css"), array("sort" => 96));

        $this->addScript($this->theme->getUrl("scripts/jquery.themepunch.revolution.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.themepunch.showbizpro.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.themepunch.plugins.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.easing.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.tooltips.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.magnific-popup.min.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.superfish.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.twitter.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.flexslider.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.jpanelmenu.js"));
        $this->addScript($this->theme->getUrl("scripts/jquery.isotope.min.js"));
        $this->addScript($this->theme->getUrl("scripts/custom.js"));
        ?>
    </head>
    <body <?= Html::renderTagAttributes(['class' => $this->document->getBodyClass(), 'style' => ($bg ? 'background-image: url(' . $bg . ')' : '')]) ?>>
        {TOOLBARPANEL}
        <!-- Header -->
        <header id="header">
            <!-- Container -->
            <div class="container">
                <!-- Logo / Mobile Menu -->
                <div class="three columns">
                    <div id="mobile-navigation">
                        <a href="#menu" class="menu-trigger"><i class="icon-reorder"></i></a>
                    </div>

                    <div id="logo">
                        <a href="<?php echo RC::app()->request->getBaseUrl() ?>"><img src="<?= $logo ?>" alt="" /></a>
                    </div>
                </div>
                <!-- Navigation -->
                <div class="thirteen columns">
                    <nav id="navigation" class="menu">                        
                        {WIDGET position="navigation" wrapper="none"}
                    </nav>
                </div>
            </div>
            <!-- Container / End -->
        </header>
        <!-- Header / End -->


        <!-- Content Wrapper / Start -->
        <div id="content-wrapper">
 
            <div class="container">                

                <div class="sixteen columns">
                    {MESSAGE}
                    <section id="not-found">
                        <h2>404 <i class="icon-question-sign"></i></h2>
                        <p><?= Text::_("TMP_DEFAULTS_TEXT_ERROR_NOTFOUND") ?></p>
                    </section>

                </div>
            </div>
        </div>

        <!-- Content Wrapper / End -->
        <!-- Footer -->
        <div id="footer" class="<?= $this->theme->param->getVar('footer') ?>">
            <!-- Container -->
            <div class="container">
                <?php if ($this->isWidget("footer-1")): ?>
                    {WIDGET position="footer-1" wrapper="footer"}
                <?php endif; ?>
                <?php if ($this->isWidget("footer-2")): ?>
                    {WIDGET position="footer-2" wrapper="footer"}
                <?php endif; ?>
                <?php if ($this->isWidget("footer-3")): ?>
                    {WIDGET position="footer-3" wrapper="footer"}
                <?php endif; ?>
                <?php if ($this->isWidget("footer-4")): ?>
                    {WIDGET position="footer-4" wrapper="footer"}
                <?php endif; ?>  
            </div>
            <!-- Container / End -->

        </div>
        <!-- Footer / End -->

        <!-- Footer Bottom / Start -->
        <div id="footer-bottom" class="<?= $this->theme->param->getVar('footer') ?>">

            <!-- Container -->
            <div class="container">

                <div class="eight columns"><?= $this->theme->param->getVar('copyright') ?></div>
                <div class="eight columns">
                    <ul class="social-icons-footer">
                        <li><a href="#" class="tooltip top" title="Twitter"><i class="icon-twitter"></i></a></li>
                        <li><a href="#" class="tooltip top" title="Facebook"><i class="icon-facebook"></i></a></li>
                        <li><a href="#" class="tooltip top" title="Dribbble"><i class="icon-dribbble"></i></a></li>
                        <li><a href="#" class="tooltip top" title="LinkedIn"><i class="icon-linkedin-rect"></i></a></li>
                        <li><a href="#" class="tooltip top" title="RSS"><i class="icon-rss"></i></a></li>
                    </ul>
                </div>

            </div>
            <!-- Container / End -->

        </div>
        <!-- Footer Bottom / Start -->

    </body>
</html>


