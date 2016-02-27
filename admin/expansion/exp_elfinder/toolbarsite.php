<?php

defined('_CHECK_') or die("Access denied");

RC::app()->document->setLangTextScritp([
    "LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG"
]);
$asset = new \ui\assets\AssetExp([
   'basePath'=>'@admin/expansion/exp_elfinder/assets'
]);
$urlAsset = $asset->getAssetBaseUrl();
RC::app()->toolbar->addGroup("elfinder",[
    "class"=>"Buttonset",
    "TITLE" => "EXP_ELFINDER_TITLE",
    "TYPE" => "BIG",
    "SORT" => 10,
    "SORTGROUP" => 100,
    "SRC" => $urlAsset."/img/icon-finder.png",
    "ACTION" => "cms.loadFileManager(); return false;"
   ]
);
?>