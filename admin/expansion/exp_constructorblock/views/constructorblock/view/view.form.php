<?php

defined('_CHECK_') or die("Access denied");

class Constructorblock_View_Constructorblock extends View {

    public function registry() {
        
        
        $title = $this->get('model')->isNewRecord ? "EXP_CONSTRUCTORBLOCK_ADD" : "EXP_CONSTRUCTORBLOCK_EDIT";
        RC::app()->breadcrumbs = ['label' => $title];
        $toolbar = RC::app()->toolbar;


        $toolbar->addGroup('block', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return saveFormBlock('#constructorblock-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return saveFormBlock('#constructorblock-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return saveFormBlock('#constructorblock-form', {action:'saveClose'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('block', [
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