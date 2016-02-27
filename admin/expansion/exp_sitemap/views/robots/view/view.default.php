<?php

defined('_CHECK_') or die("Access denied");

class Sitemap_View_Robots extends View {

    public function registry() {
        RC::app()->breadcrumbs = ['label' => "EXP_SITEMAP_ROBOTS"];
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
            "ACTION" => "return cms.btnGridActionPromt('#sitemap-robots-grid', this.href)",
        ]);
        
    }

}

?>