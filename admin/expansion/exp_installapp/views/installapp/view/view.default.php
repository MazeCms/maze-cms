<?php defined('_CHECK_') or die("Access denied");

class Installapp_View_Installapp extends View {

    public function registry() {
        RC::app()->breadcrumbs = ['label' => 'EXP_INSTALLAPP_UNINSTALL_TITLE'];
    }
}

?>