<?php

defined('_CHECK_') or die("Access denied");

class Installapp_View_Install extends View {

    public function registry() {
        
        RC::app()->breadcrumbs = ['label' => 'EXP_INSTALLAPP_INSTALL_TITLE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('install', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_INSTALLAPP_ACTION_INSTALL",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 12,
            "ID"=>"upload-install",
            "HREF" => [['run' => 'add']],
            "VISIBLE" =>true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "return false;",
        ]);
        
    }

}

?>