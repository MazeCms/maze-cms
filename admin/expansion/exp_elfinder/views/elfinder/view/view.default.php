<?php

defined('_CHECK_') or die("Access denied");

class Elfinder_View_Elfinder extends View {

    public function registry() {
        
        RC::app()->breadcrumbs = ['label' => 'EXP_ELFINDER_PROFILE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('elfinder', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ELFINDER_BTN_ADDPROFILE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add']],
            "VISIBLE" =>$this->_access->roles("elfinder", "EDIT_PROFILE"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);

       
        $toolbar->addGroup('elfinder', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("elfinder", "EDIT_PROFILE"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#elfinder-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#elfinder-grid', this.href)"
                ]
            ]
        ]);
        
      
        $toolbar->addGroup('elfinder', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ELFINDER_BTN_DELETEPROFILE",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("elfinder", "DELETE_PROFILE"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
            "ACTION" => "return cms.btnGridActionPromt('#elfinder-grid', this.href)"
        ]);

    }

}

?>