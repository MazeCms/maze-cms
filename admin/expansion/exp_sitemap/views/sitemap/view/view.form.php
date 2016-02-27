<?php

defined('_CHECK_') or die("Access denied");

class Sitemap_View_Sitemap extends View {

    public function registry() {
        
        
        $title = $this->get('model')->map->isNewRecord ? "EXP_SITEMAP_ADD" : "EXP_SITEMAP_EDIT";
        RC::app()->breadcrumbs = ['label' => $title];
        $toolbar = RC::app()->toolbar;


        $toolbar->addGroup('sitemap', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return saveFormBlock('#sitemap-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return saveFormBlock('#sitemap-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return saveFormBlock('#sitemap-form', {action:'saveClose'})"
                ]
            ]
        ]);
       $toolbar->addGroup('sitemap', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
        $toolbar->addGroup('sitemap', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_IMPORT",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>9,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-syncalt.png",
            "ACTION" => "return actionImportLinks('#sitemap-form-import');"
        ]);
        
         $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => true,
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return enableTreeLink();"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return disableTreeLink();"
                ]
            ]
        ]);
         
         $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_LABEL_EXPANDALL",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 5,
            "VISIBLE" => true,
            "SORTGROUP" => 5,
            "SRC" => RC::app()->getExpUrl('images/icon-chevron-down.png'),
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_SITEMAP_LABEL_EXPANDALL',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => RC::app()->getExpUrl('images/icon-chevron-down.png'),
                    "ACTION" => "$('#maps-link-table').treetable('expandAll'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_SITEMAP_LABEL_COLLAPSE',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => RC::app()->getExpUrl('images/icon-chevron-up.png'),
                    "ACTION" => "$('#maps-link-table').treetable('collapseAll'); return false;"
                ]
            ]
        ]);
        
         $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_LABEL_MASSACTION",
            "TYPE" => Buttonset::BTNMIN,
            "ID"=>"edit-tree-table",
            "SORT" => 3,
            "VISIBLE" => true,
            "SORTGROUP" => 5,
            "SRC" => RC::app()->getExpUrl('images/icon-edit.png'),
            "ACTION" => "return false;"
         ]);
    }

}

?>