<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\FileHelper;
use maze\table\InstallApp;
use maze\table\ContentType;

$theme = RC::app()->theme;
$types = ContentType::find()->where(['expansion' => 'contents'])->all();
$menu = [];
$router = RC::app()->router;
 
foreach ($types as $type) {
    $title = $type->title;
    if (!empty($type->description)) {
        $title .= '<span class="menu-help">' . $type->description . '</span>';
    }
    $menu[] = [
        "class" => 'ContextMenu',
        "TITLE" => $title,
        "HREF" => ['/admin/contents', ['run' => 'add', 'bundle' => $type->bundle, "return"=>URI::current()]],
        "ACTION" => "window.open(this.href); return false;"
    ];
}
if ($menu) {
    RC::app()->getToolbar()->addGroup("contents", [
        "class" => "Buttonset",
        "TITLE" => "EXP_CONTENTS_CONTENTS_ADD",
        "TYPE" => "BIG",
        "SORT" => 10,
        "VISIBLE" => ($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS")),
        "SORTGROUP" => 10,
        "HINT" => array("TITLE" => "EXP_CONTENTS_NAME", "TEXT" => "EXP_CONTENTS_DESCPRIPTION"),
        "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
        "MENU" => $menu
            ]
    );

   
    $id = RC::app()->request->get('contents_id');
    if ($router && $router->component == 'contents' && $router->controller == 'controller' && $id) {
        RC::app()->getToolbar()->addGroup("contents", [
            "class" => "Buttonset",
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
            "TYPE" => "BIG",
            "SORT" => 9,
            "VISIBLE" => ($this->access->roles("contents", "EDIT_CONTENTS") || $this->access->roles("contents", "EDIT_SELF_CONTENTS")),
            "SORTGROUP" => 10,
            "HINT" => array("TITLE" => "EXP_CONTENTS_NAME", "TEXT" => "EXP_CONTENTS_DESCPRIPTION"),
            "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $id]],
            "ACTION" => "cms.formDialogSave(this,{title:'".Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME",['name'=>'ID: '.$id])."'}); return false;",
            "SRC" => "/library/jquery/toolbarsite/images/big-edit-doc.png",
            "MENU" => [
                [
                    'class' => 'ContextMenu',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                    "SORT" => 1,
                    "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $id, "return"=>URI::current()]],
                    "ACTION" => "window.open(this.href); return false;"
                ],
                [
                    'class' => 'ContextMenu',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                    "SORT" => 1,
                    "HREF" => ['/admin/contents', ['run' => 'edit', 'contents_id' => $id, "return"=>URI::current()]],
                ]
            ]
                ]
        );
    }    
}
$term_id = RC::app()->request->get('term_id');
if ($router && $router->component == 'contents' && $router->controller == 'category' && $term_id && $this->access->roles("dictionary", "VIEW_ADMIN")) {
    
    RC::app()->getToolbar()->addGroup("contents", [
            "class" => "Buttonset",
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
            "TYPE" => "BIG",
            "SORT" => 10,
            "VISIBLE" => $this->access->roles("dictionary", "EDIT_TERM"),
            "SORTGROUP" => 10,
            "HINT" => array("TITLE" => "EXP_CONTENTS_NAME", "TEXT" => "EXP_CONTENTS_DESCPRIPTION"),
            "HREF" => ['/admin/dictionary/term', ['run' => 'edit', 'term_id' => $term_id]],
            "ACTION" => "cms.formDialogSave(this,{title:'".Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME",['name'=>'ID: '.$term_id])."'}); return false;",
            "SRC" => "/library/jquery/toolbarsite/images/big-edit-folder.png",
            "MENU" => [
                [
                    'class' => 'ContextMenu',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                    "SORT" => 1,
                    "HREF" => ['/admin/dictionary/term', ['run' => 'edit', 'term_id' => $term_id, "return"=>URI::current()]],
                    "ACTION" => "window.open(this.href); return false;"
                ],
                [
                    'class' => 'ContextMenu',
                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                    "SORT" => 1,
                    "HREF" => ['/admin/dictionary/term', ['run' => 'edit', 'term_id' => $term_id, "return"=>URI::current()]],
                ]
            ]
                ]
        );
}
?>