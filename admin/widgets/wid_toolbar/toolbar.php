<?php

defined('_CHECK_') or die("Access denied");
use maze\helpers\Json;
wid\wid_toolbar\AssetWidget::register();

$options = [
   'tooltipClass'=>'dark-tooltip-bar',
    'show'=>['effect'=>'fade', 'delay'=>500, 'speed'=>100],
    'hide'=>['effect'=>'fade', 'delay'=>500],
    'position'=>['my'=>'left top', 'at'=>'left bottom+5']
];
$this->document->setTextScritp('$("#title-app-name").tooltip('.Json::encode($options).'); '
        . 'if($("#tool-bar-admin").is("#tool-bar-admin"))$("#tool-bar-admin").toolBarAdmin();',['wrap'=>\Document::DOCREADY]);

$mess = $this->document->getMessage();

echo $this->render('tmp/toolbar', ['mess'=>$mess]);
?>