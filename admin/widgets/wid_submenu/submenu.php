<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\Json;
use maze\base\JsExpression;
use wid\wid_submenu\helpers\Tree;
ui\assets\AssetTree::register();
(new ui\assets\AssetTree(['js'=>null, 'css'=>['themes/default/style.css', 'themes/default-dark/style.css']]))->registerAssetFiles();

wid\wid_submenu\AssetWidget::register();

$option = [
    'core' => [
        "check_callback" => true,
        'themes' => ['name' => 'default-dark'],
    ],
    'plugins' => ['types', 'wholerow']
];

$optionsTool = [
    'tooltipClass' => 'dark-tooltip-bar',
    'show' => ['effect' => 'fade', 'delay' => 500, 'speed' => 100],
    'hide' => ['effect' => 'fade', 'delay' => 500],
    'position' => ['my' => 'left top', 'at' => 'right top', 'of' => new JsExpression('$(this).parent()')]
];

$this->document->setTextScritp('$("#admin-app-menu").jstree(' . Json::encode($option) . ')'
        . '.on("select_node.jstree", function(e, obj){'
        . 'if($(obj.event.target).is("[onclick]")){}else if($(obj.event.target).is("[href]")){document.location = $(obj.event.target).attr("href") }'
        . '});'
        . '$("#admin-app-menu").on("ready.jstree after_open.jstree", function (e, data) {$("#admin-app-menu [title]").each(function(){$(this).tooltip(' . Json::encode($optionsTool) . ');}) })', ['wrap' => \Document::DOCREADY]);


echo '<div id="admin-app-menu">';
Tree::renderMenu(RC::app()->menu->createMenu());
echo '</div>';
?>