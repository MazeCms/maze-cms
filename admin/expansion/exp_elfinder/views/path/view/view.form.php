<?php

defined('_CHECK_') or die("Access denied");

class Elfinder_View_Path extends View {

    public function registry() {

        $title = $this->get('modelForm')->path_id ? "EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT" : "EXP_ELFINDER_BTN_ADD" ;

        RC::app()->breadcrumbs = ['label'=>'EXP_ELFINDER_DIR_PATH', 'url'=>['/admin/elfinder/path', ['profile_id' => $this->get('profile_id')]]];
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
            "ACTION" => "return cms.btnFormAction('#elfinder-path-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#elfinder-path-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#elfinder-path-form', {action:'saveClose'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['profile_id' => $this->get('profile_id')]],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "this.href"
        ]);
  

    }

   
}

?>