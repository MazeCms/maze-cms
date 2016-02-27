<?php

defined('_CHECK_') or die("Access denied");

class Menu_View_Menu extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        
        RC::app()->breadcrumbs = ['label'=>'Меню'];
        
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_ADD_GROUP",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF"=>[['run'=>'add']],
            "VISIBLE"=>$this->_access->roles("menu", "EDIT_MENU"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png",
            "ACTION" => "this.href",
        ]);
        
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_COPY_GROUP",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "VISIBLE"=>$this->_access->roles("menu", "EDIT_MENU"),
            "HREF"=>[['run'=>'copy']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnGridAction('#menugroup-grid', this.href)",
        ]);
        
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_DELETE_GROUP",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>$this->_access->roles("menu", "DELET_MENU"),
            "HREF"=>[['run'=>'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#menugroup-grid', this.href)",
        ]);

    }

}

?>