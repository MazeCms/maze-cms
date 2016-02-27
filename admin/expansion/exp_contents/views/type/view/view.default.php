<?php

defined('_CHECK_') or die("Access denied");

class Contents_View_Type extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        
        RC::app()->breadcrumbs = ['label'=>'EXP_CONTENTS_TYPE'];
     
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_ADD_TYPE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF"=>[['run'=>'add']],
            "VISIBLE"=>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png",
            "ACTION" => "this.href",
        ]);
        
       

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_DELETE_TYPE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>$this->_access->roles("contents", "DELETE_TYPE_CONTENTS"),
            "HREF"=>[['run'=>'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#contents-type-grid', this.href)",
        ]);

    }

}

?>