<?php

defined('_CHECK_') or die("Access denied");


class Dictionary_View_Dictionary extends View {

    public function registry() {

        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_DICTIONARY_TYPE_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF"=>[['run'=>'add']],
            "VISIBLE"=>$this->_access->roles("dictionary", "EDIT_DICTIONARY"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png",
            "ACTION" => "this.href",
        ]);
        
 
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_DICTIONARY_TYPE_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>$this->_access->roles("dictionary", "DELETE_DICTIONARY"),
            "HREF"=>[['run'=>'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#dictionary-grid', this.href)",
        ]);
    }

}

?>