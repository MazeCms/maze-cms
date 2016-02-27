<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Db extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_DB_NAME', 'url'=>['/admin/logs/db']];
        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_READMORE'];
    }

}

?>