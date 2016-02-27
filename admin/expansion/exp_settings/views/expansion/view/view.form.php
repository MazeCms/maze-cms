<?php

defined('_CHECK_') or die("Access denied");

class Settings_View_Expansion extends View {

    public function registry() {
        $modelForm = $this->get('modelForm');
        $exp = $this->get('exp');
        $xmlParams = RC::getConf(["name" => $exp->name, "type" => "expansion"], $modelForm->param);
        $params = $xmlParams->getParams();
        
        
        RC::app()->breadcrumbs = ['label' => "EXP_SETTINGS_SUBMENU_EXPANSION", 'url'=>['/admin/settings/expansion']];
        RC::app()->breadcrumbs = ['label' => $xmlParams->get('name')];
        $toolbar = RC::app()->toolbar;      
        $toolbar->addGroup('exp', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#expansion-app-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#expansion-app-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#expansion-app-form', {action:'saveClose'})"
                ]
            ]
        ]);
        $toolbar->addGroup('exp', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
        $this->set('xmlParams', $xmlParams);
        $this->set('params', $params);

    }

    
}

?>