<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Request extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_REQUEST_NAME', 'url'=>['/admin/logs/request']];
        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_READMORE'];
    }

}

?>