<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\FileHelper;
use maze\table\InstallApp;
use maze\table\ContentType;

$theme = RC::app()->theme;
$types = ContentType::find()->where(['expansion' => 'dictionary'])->all();
$menu = [];
foreach ($types as $type) {
    $title = $type->title;
    if (!empty($type->description)) {
        $title .= '<span class="menu-help">' . $type->description . '</span>';
    }
    $menu[] = [
        "class" => 'ContextMenu',
        "TITLE" => $title,
        "HREF" => ['/admin/dictionary/term', ['run' => 'add', 'bundle' => $type->bundle]],
        "ACTION" => "window.open(this.href); return false;"
    ];
}

RC::app()->getToolbar()->addGroup('contents', [
    'class' => 'Buttonset',
    "TITLE" => "EXP_DICTIONARY_TERM_ADD",
    "TYPE" => Buttonset::BTNBIG,
    "SORT" => 10,
    "VISIBLE" => $this->access->roles("dictionary", "EDIT_TERM"),
    "SORTGROUP" => 10,
    "HINT" => array("TITLE" => "EXP_DICTIONARY_NAME", "TEXT" => "EXP_DICTIONARY_DESCPRIPTION"),
    "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png",
    "MENU" => $menu
]);
?>