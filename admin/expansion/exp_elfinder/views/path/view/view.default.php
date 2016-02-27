<?php defined('_CHECK_') or die("Access denied");

class Elfinder_View_Path extends View {

    public function registry() {
        
        RC::app()->breadcrumbs = ['label' => 'EXP_ELFINDER_PROFILE', 'url'=>['/admin/elfinder/']];        
        RC::app()->breadcrumbs = ['label' => $this->get('profile')->title];

        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('elfinder', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ELFINDER_BTN_ADDDIR",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add', 'profile_id'=>$this->get('profile')->profile_id]],
            "VISIBLE" =>$this->_access->roles("elfinder", "EDIT_PATH"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);

       
        
        $toolbar->addGroup('elfinder', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_ELFINDER_BTN_DELETEDIR",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("elfinder", "DELETE_PATH"),
            "HREF" => [['run' => 'delete', 'profile_id'=>$this->get('profile')->profile_id]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#elfinder-dir-grid', this.href)"
        ]);

    }

}

?>