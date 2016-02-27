<?php

defined('_CHECK_') or die("Access denied");

class User_View_Role extends View {

    public function registry() {        
        RC::app()->breadcrumbs = ['label' => 'EXP_USER_ROLE_MENU_TITLE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_ROLE_MENU_CREATE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add']],
            "VISIBLE" => $this->_access->roles("user", "EDIT_ROLE"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);
        
        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_ROLE_MENU_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("user", "DELET_USER"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#role-grid', this.href)"
        ]);
    }
}

?>