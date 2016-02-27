<?php

defined('_CHECK_') or die("Access denied");

use maze\table\ContentType;

class Contents_View_Home extends View {

    public function registry() {

        $toolbar = RC::app()->toolbar;

        RC::app()->breadcrumbs = ['label' => 'EXP_CONTENTS_CONT_BTNHOME'];
    }

}

?>