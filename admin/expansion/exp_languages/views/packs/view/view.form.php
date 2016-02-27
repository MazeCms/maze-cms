<?php

defined('_CHECK_') or die("Access denied");

class Languages_View_Packs extends View {

    public function registry() {
     
        RC::app()->breadcrumbs = ['label' => 'EXP_LANGUAGES_PACKS_NAME', 'url'=>['/admin/languages/packs']];
        RC::app()->breadcrumbs = ['label' => 'EXP_LANGUAGES_PACKS_FORM_TITLE'];
        $toolbar = RC::app()->toolbar;
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#applications-pack-grid')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#applications-pack-grid')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#applications-pack-grid', {action:'saveClose'})"
                ]
            ]
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "SORTGROUP" => 10,
            "HREF" => [['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);

    }

}

?>