<?php

defined('_CHECK_') or die("Access denied");

class Contents_View_Type extends View {

    public function registry() {


       // $title = $menu ? "EXP_MENU_VIEW_ADD_TITLE_EDIT" : "EXP_MENU_VIEW_ADD_TITLE_CREATE";

        RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_TYPE', 'url'=>['/admin/contents/type']];
        RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_ADD_TYPE'];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true, //$this->_access->roles("menu", "EDIT_MENU"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#contents-form-type')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#contents-form-type')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#contents-form-type', {action:'saveClose'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'Сохранить и добавить поля',
                    "SORT" => -1,
                    "ACTION" => "return cms.btnFormAction('#contents-form-type', {action:'saveAddField'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('contents', [
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