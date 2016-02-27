<?php

defined('_CHECK_') or die("Access denied");

class Templating_View_Templating extends View {

    public function registry() {
        
        RC::app()->breadcrumbs = ['label' => 'EXP_TEMPLATING_STYLE_TABLE_TITLE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => "Создать",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add']],
            "VISIBLE" =>$this->_access->roles("templating", "EDIT_STYLE"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);

        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => "Копировать",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
            "HREF" => [['run' => 'copy']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnGridAction('#style-grid', this.href)",
        ]);


        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => "Удалить",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("templating", "DELET_STYLE"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#style-grid', this.href)"
        ]);

    }

}

?>