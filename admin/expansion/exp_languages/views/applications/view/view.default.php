<?php

defined('_CHECK_') or die("Access denied");

class Languages_View_Applications extends View {

    public function registry() {
        RC::app()->breadcrumbs = ['label' => "EXP_LANGUAGES_APP"];
        $toolbar = RC::app()->toolbar;
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_LANGUAGES_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add']],
            "VISIBLE" =>$this->_access->roles("languages", "EDIT_LANG_APP"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("languages", "EDIT_LANG_APP"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#applications-lang-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#applications-lang-grid', this.href)"
                ]
            ]
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_WIDGET_WIDGETS_TABLE_BODY_DEL",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("languages", "DELET_LANG_APP"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
            "ACTION" => "return cms.btnGridActionPromt('#applications-lang-grid', this.href)"
        ]);
    }

}

?>