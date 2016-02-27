<?php defined('_CHECK_') or die("Access denied");

 wid\wid_navbar\AssetWidget::register();

//$this->document->addScript('/library/jquery/raphael/raphael.js');
$this->document->setLangTextScritp(["WID_NAVBAR_EDITS", "WID_NAVBAR_OUT"]);
//$this->document->setTextScritp('new LogoLabyrinth({appendTo:"#logo-cms",radius:4,width:4})', ['wrap'=>Document::DOCREADY]);
$user = RC::app()->access->get();

echo $this->render('tmp/navbar', ['user'=>$user]);

?>
