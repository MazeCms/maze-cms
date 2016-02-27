<!DOCTYPE HTML>
<html lang="<?php echo $this->document->get('language') ?>" dir="ltr" xml:lang="<?php echo $this->document->get('language') ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
$doc = $this->document;

$doc->_title = Text::_("TMP_ADMINTMP_LOGO_TITLE");

?>
{HEADER}
</head>

<body>
<div id="radial-gradient-wrapp"></div>
{WIDGET position="login" wrapper="none"}


</body>
</html>