<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Error extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_ERROR_NAME', 'url'=>['/admin/logs/error']];
        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_READMORE'];
    }

}

?>