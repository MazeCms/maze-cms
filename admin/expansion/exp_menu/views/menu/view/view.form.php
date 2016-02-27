<?php

defined('_CHECK_') or die("Access denied");

class Menu_View_Menu extends View {

    public function registry() {


        $this->_doc->setLangTextScritp(array(
            "EXP_MENU_ADD_ITEM_ALERT_TITLE",
            "EXP_MENU_VIEW_ADD_ALERT_TEXT"
        ));

        $modelForm = $this->get('modelForm');

        $title = $modelForm->scenario !== 'create' ? "EXP_MENU_VIEW_ADD_TITLE_EDIT" : "EXP_MENU_VIEW_ADD_TITLE_CREATE";

        RC::app()->breadcrumbs = ['label'=>'Меню', 'url'=>['/admin/menu']];
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("menu", "EDIT_MENU"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#menu-form-menu')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#menu-form-menu')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#menu-form-menu', {action:'saveClose'})"
                ]
            ]
        ]);
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "VISIBLE" => $modelForm->id_group ? $this->_access->roles("menu", "EDIT_MENU") : false,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnFormAction('#menu-form-menu', {action:'copy'})",
            "MENU" => [                
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECOPY_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#menu-form-menu', {action:'saveCopy'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#menu-form-menu', {action:'copy'})"
                ]
            ]
        ]);
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);


    }

}

?>