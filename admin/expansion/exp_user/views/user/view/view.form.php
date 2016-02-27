<?php

defined('_CHECK_') or die("Access denied");

class User_View_User extends View {

    public function registry() {

        $modelForm = $this->get('modelForm');
        $title = $modelForm->id_user ? "EXP_USER_FORM_TITLE_EDIT" : "EXP_USER_FORM_TITLE_NEW";
        if($this->_access->roles("user", "VIEW_ADMIN")){
            RC::app()->breadcrumbs = ['label' => 'EXP_USER_TITLE', 'url' => ['/admin/user']];
        }
        
        RC::app()->breadcrumbs = ['label' => $title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("user", "EDIT_USER") || $this->_access->roles("user", "EDIT_SELF_USER"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#user-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "VISIBLE" => $this->_access->roles("user", "VIEW_ADMIN"),
                    "ACTION" => "return cms.btnFormAction('#user-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "VISIBLE" => $this->_access->roles("user", "VIEW_ADMIN"),
                    "ACTION" => "return cms.btnFormAction('#user-form', {action:'saveClose'})"
                ]
            ]
        ]);

        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "VISIBLE" => $this->_access->roles("user", "VIEW_ADMIN"),
            "SORTGROUP" => 10,
            "HREF" => [['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
    }


}

?>