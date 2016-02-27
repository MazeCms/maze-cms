<?php

defined('_CHECK_') or die("Access denied");

class Admin_View_Admin extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('admin', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ADMIN_ADDDESKTOP",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("admin", "ADD_DESKTOP"),
            "SORTGROUP" => 10,
            "HREF"=>[['run'=>'addDesktopFrom']],
            "ACTION"=>"return adminBar.desktopForm(this)",
            "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png"
        ]);
        $toolbar->addGroup('admin', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ADMIN_ADDGADGET",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "VISIBLE" =>($this->get('id_des') && $this->get('marking') && $this->_access->roles("admin", "ADD_GADGET")),
            "SORTGROUP" => 10,
            "HREF"=>[['run'=>'addGadget', 'id_des'=>$this->get('id_des')]],
            "ACTION"=>" cms.loadDialog({url:this.href, title:cms.getLang('EXP_ADMIN_ADDGADGET'), width:700}); return false;",
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png"
        ]);
        $toolbar->addGroup('admin', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ADMIN_DELETEDESKTOP",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => ($this->get('id_des') && $this->get('marking') && $this->_access->roles("admin", "DELETE_DESKTOP")),
            "HREF" => [['run' => 'deleteDesktop', 'id_des'=>$this->get('id_des')]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return adminBar.deleteDesktop(this);"
        ]);
    }

}

?>