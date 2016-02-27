<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Cache extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_CACHE_NAME', 'url'=>['/admin/logs/cache']];
        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_READMORE'];
    }

}

?>