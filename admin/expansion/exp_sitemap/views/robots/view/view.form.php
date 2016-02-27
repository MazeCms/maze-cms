<?php

defined('_CHECK_') or die("Access denied");

class Sitemap_View_Robots extends View {

    public function registry() {
        
         RC::app()->breadcrumbs = ['label' => "EXP_SITEMAP_ROBOTS", 'url'=>['admin/sitemap/robots']];
        $title = $this->get('model')->isNewRecord ? "EXP_SITEMAP_ADD" : "EXP_SITEMAP_EDIT";
        RC::app()->breadcrumbs = ['label' => $title];
        $toolbar = RC::app()->toolbar;


        $toolbar->addGroup('sitemap', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#sitemap-robots-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#sitemap-robots-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#sitemap-robots-form', {action:'saveClose'})"
                ]
            ]
        ]);
       $toolbar->addGroup('sitemap', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
       
    }

}

?>