<?php

defined('_CHECK_') or die("Access denied");

class Dictionary_View_Dictionary extends View {

    public function registry() {


        $title = $this->get('modelForm')->scenario == 'create' ?  'EXP_DICTIONARY_ADD_TYPE' : 'EXP_DICTIONARY_EDIT_TYPE';
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true, //$this->_access->roles("menu", "EDIT_MENU"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#dictionary-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#dictionary-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#dictionary-form', {action:'saveClose'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_DICTIONARY_TYPE_ADD_FIELD',
                    "SORT" => -1,
                    "ACTION" => "return cms.btnFormAction('#dictionary-form', {action:'saveAddField'})"
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