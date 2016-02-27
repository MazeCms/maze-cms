<?php

defined('_CHECK_') or die("Access denied");

class Settings_View_Expansion extends View {

    public function registry() {
         RC::app()->breadcrumbs = ['label' => "EXP_SETTINGS_SUBMENU_EXPANSION"];
        $toolbar = RC::app()->toolbar;      
        
        $toolbar->addGroup('expansion', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" =>true,
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#settings-expansion-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#settings-expansion-grid', this.href)"
                ]
            ]
        ]);
        $toolbar->addGroup('expansion', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SETTINGS_EXPANSION_BTN_CLEARCACHE",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 8,
            "VISIBLE" =>true,
            "SORTGROUP" => 5,
            "HREF" => [['run' => 'clear']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
            "ACTION" => "return cms.btnGridAction('#settings-expansion-grid', this.href)"
        ]);
        $toolbar->addGroup('expansion', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SETTINGS_EXPANSION_BTN_DEFAULT",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 9,
            "VISIBLE" =>true,
            "SORTGROUP" => 5,
            "HREF" => [['run' => 'refresh']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-settings.png",
            "ACTION" => "return cms.btnGridAction('#settings-expansion-grid', this.href)"
        ]);

    }

    
}

?>