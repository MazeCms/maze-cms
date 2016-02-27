<?php
    use maze\helpers\Html;
?>
<!DOCTYPE HTML>
<html <?=Html::renderTagAttributes(['class'=>$this->document->getHtmlClass()])?> lang="<?php echo $this->document->get('language') ?>" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {HEADER}
        <?php
            $this->addStylesheet($this->theme->getUrl("/css/reset.css"));
        ?>
    </head>
    <body <?=Html::renderTagAttributes(['class'=>$this->document->getBodyClass()])?>>
<?php if ($this->isMessage()): ?>
        {MESSAGE}
<?php endif; ?>
        {CONTENT}
    </body>
</html>
