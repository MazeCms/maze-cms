<?php

defined('_CHECK_') or die("Access denied");

class User_View_Recover extends View {

    public function registry() {
        $this->_doc->set("title", Text::_("EXP_USER_RECOVER_PASS"));
    }

}

?>