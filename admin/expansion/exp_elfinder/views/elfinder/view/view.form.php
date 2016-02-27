<?php

defined('_CHECK_') or die("Access denied");

class Elfinder_View_Elfinder extends View {

    public function registry() {

        $title = $this->get('modelForm')->profile_id ? "EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT" : "EXP_ELFINDER_BTN_ADD" ;

        RC::app()->breadcrumbs = ['label'=>'EXP_ELFINDER_PROFILE', 'url'=>['/admin/elfinder']];
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" =>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#elfinder-profile-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#elfinder-profile-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#elfinder-profile-form', {action:'saveClose'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>['/admin/elfinder'],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
  
        
        $button = ["back", "forward", "reload", "up", "home", "mkdir",
            "mkfile", "upload", "open", "download", "getfile", "info", "quicklook",
            "copy", "cut", "paste", "rm", "duplicate", "rename", "edit", "resize",
            "extract", "archive", "search", "view", "sort", "help"];
        
        $this->set('button', $button);
    }

   
}

?>