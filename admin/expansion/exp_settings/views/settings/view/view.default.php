<?php defined('_CHECK_') or die("Access denied");

class Settings_View_Settings extends View {

    public function registry() {
        
        RC::app()->breadcrumbs = ['label' => 'EXP_SETTINGS_SUBMENU_SYSTEM'];
        $toolbar = RC::app()->toolbar;
        
        $toolbar->addGroup('system', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" =>  $this->_access->roles("settings", "EDIT_SYS_SERVER"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#settings-form')",
        ]);
        
       

    }

    

}

?>