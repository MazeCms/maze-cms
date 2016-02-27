<?php

defined('_CHECK_') or die("Access denied");

class Templating_View_Template extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' => 'EXP_TEMPLATING_TMP_TABLE_TITLE'];
    }

}

?>