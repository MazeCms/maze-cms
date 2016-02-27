<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Exp extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' => 'EXP_LOGS_EXT_NAME'];
       
        RC::app()->toolbar->addGroup('logo', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_LOGS_ACTION_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => 1,
            "HREF" => ['admin/logs', ['run' => 'delete', 'type'=>'exp']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "this.href",
        ]);
    }

}

?>