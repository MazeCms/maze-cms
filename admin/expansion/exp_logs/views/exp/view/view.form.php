<?php

defined('_CHECK_') or die("Access denied");

class Logs_View_Exp extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_EXT_NAME', 'url'=>['/admin/logs/exp']];
        RC::app()->breadcrumbs = ['label' =>'EXP_LOGS_READMORE'];
    }

}

?>