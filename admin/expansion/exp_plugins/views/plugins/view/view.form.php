<?php

defined('_CHECK_') or die("Access denied");

class Plugins_View_Plugins extends View {

    public function registry() {

        $modelTable = $this->get('modelTable');
        
        
        $xmlParams = RC::getConf(["type" => "plugin", "group" => $modelTable->group_name, "name" => $modelTable->name, "front" => $modelTable->installApp->front_back], $modelTable->param);

        RC::app()->breadcrumbs = ['label' => $xmlParams->get('name')];
        
        $params = $xmlParams->getParams();
        
         $toolbar = RC::app()->toolbar;
         $toolbar->addGroup('plugin', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#plugin-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#plugin-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#plugin-form', {action:'saveClose'})"
                ]
            ]
        ]);
         
        $toolbar->addGroup('plugin', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]); 

        $this->set("params", $params);
        $this->set("xmlParams", $xmlParams);
    }

}

?>