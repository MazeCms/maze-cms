<?php

defined('_CHECK_') or die("Access denied");

class Languages_View_Packs extends View {

    public function registry() {
        RC::app()->breadcrumbs = ['label' => 'EXP_LANGUAGES_PACKS_NAME'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_LANGUAGES_PACKS_BTN_ADDVALUE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 12,
            "HREF" => [['run' => 'add']],
            "VISIBLE" =>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_LANGUAGES_PACKS_BTN_INDEX",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "HREF" => [['run' => 'index']],
            "VISIBLE" =>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-syncalt.png",
            "ACTION" => "actionIndexLang(); return false;",
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_WIDGET_WIDGETS_TABLE_BODY_DEL",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("languages", "DELET_LANG_PACKS"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#applications-pack-grid', this.href)"
        ]);
    }


}

?>