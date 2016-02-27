<?php

defined('_CHECK_') or die("Access denied");

class User_View_User extends View {

    public function registry() {
        RC::app()->breadcrumbs = ['label' => 'EXP_USER_TITLE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_TOOLBAR_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add']],
            "VISIBLE" => $this->_access->roles("user", "EDIT_USER"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);

        $toolbar->addGroup('usermin', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("user", "EDIT_USER"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#user-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#user-grid', this.href)"
                ]
            ]
        ]);

        $toolbar->addGroup('usermin', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 4,
            "HREF" => [['run' => 'pack']],
            "VISIBLE" => $this->_access->roles("user", "EDIT_USER"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
            "ACTION" => "return cms.btnGridHandler('#user-grid', this.href, {title:'" . Text::_('LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON') . "'})"
        ]);
        
        $toolbar->addGroup('usermin', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_TABLE_TITLEMENU_SENDMAIL",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 2,
            "HREF" => [['run' => 'send']],
            "VISIBLE" => $this->_access->roles("user", "EDIT_USER"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-emailalt.png",
            "ACTION" => "return cms.btnGridHandler('#user-grid', this.href, {title:'" . Text::_('EXP_USER_TABLE_TITLEMENU_SENDMAIL') . "'})"
        ]);

        $toolbar->addGroup('user', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_USER_TOOLBAR_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("user", "DELET_USER"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#user-grid', this.href)"
        ]);
    }
}

?>