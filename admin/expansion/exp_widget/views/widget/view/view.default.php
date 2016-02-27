<?php

defined('_CHECK_') or die("Access denied");

class Widget_View_Widget extends View {

    public function registry() {

        $title = ($this->get('front') ? 'EXP_WIDGET_WIDGETS_FILTER_FORNT_SITE' : 'EXP_WIDGET_WIDGETS_FILTER_FORNT_ADMIN');
         RC::app()->breadcrumbs = ['label' => $title];
         
         
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('wid', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_WIDGET_FORM_TITLE_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'widgets', 'front'=>$this->get('front')]],
            "VISIBLE" =>$this->_access->roles("widget", "EDIT_WIDGET"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "cms.loadDialog({url:this.href, width:300,minHeight:150, title:'".Text::_($title)."'}); return false;",
        ]);

        $toolbar->addGroup('wid', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_WIDGET_SUBMENU_TREE_COPY",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "VISIBLE" => $this->_access->roles("widget", "EDIT_WIDGET"),
            "HREF" => [['run' => 'copy', 'front'=>$this->get('front')]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnGridAction('#widgets-grid', this.href)",
        ]);
        
        
        
        $toolbar->addGroup('wid', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_WIDGET_WIDGETS_TABLE_BODY_DEL",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("widget", "DELET_WIDGET"),
            "HREF" => [['run' => 'delete', 'front'=>$this->get('front')]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#widgets-grid', this.href)"
        ]);
        
        $toolbar->addGroup('widgroup', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("widget", "EDIT_WIDGET"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish', 'front'=>$this->get('front')]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#widgets-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish', 'front'=>$this->get('front')]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#widgets-grid', this.href)"
                ]
            ]
        ]);
        
        $toolbar->addGroup('widgroup', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 6,
            "HREF" => [['run' => 'pack', 'front'=>$this->get('front')]],
            "VISIBLE" =>$this->_access->roles("widget", "EDIT_WIDGET"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
            "ACTION" =>"return cms.btnGridHandler('#widgets-grid', this.href, {title:'".Text::_('LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON')."'})"
        ]);
        
        $toolbar->addGroup('widgroup', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_MOVING_BUTTON",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 6,
            "HREF" => [['run' => 'moving', 'front'=>$this->get('front')]],
            "VISIBLE" =>$this->_access->roles("widget", "EDIT_WIDGET"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/asinhron.png",
            "ACTION" => "return cms.btnGridHandler('#widgets-grid', this.href, {title:'".Text::_('LIB_USERINTERFACE_TOOLBAR_MOVING_BUTTON')."'})"
        ]);
        

    }


}

?>