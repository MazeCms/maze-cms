<?php

defined('_CHECK_') or die("Access denied");

class Sitemap_View_Sitemap extends View {

    public function registry() {
        
        $toolbar = RC::app()->toolbar;
        
        $toolbar->addGroup('block', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_ADDACTION",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF"=>[['run'=>'add']],
            "VISIBLE"=>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);
        
        $toolbar->addGroup('block', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_SITEMAP_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE"=>true,
            "HREF"=>[['run'=>'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#sitemap-grid', this.href)",
        ]);
        
    }

}

?>