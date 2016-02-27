<?php

defined('_CHECK_') or die("Access denied");

class Menu_View_Groupmenu extends View {

    public function registry() {

        $menuSite = maze\menu\MenuSite::instance();
      
        $this->set('menuSite', $menuSite);
        $xmlParams = $this->findParams();
        $urlparams = null;
        $params = null;
        $modelForm = $this->get('modelForm');
        if ($xmlParams) {
            $urlparams = $xmlParams->getXML()->urlparams;
            $params = $xmlParams->getParams();
            $xmlParams->setValue($modelForm->param);
            $xmlParams->merge($modelForm->url_param);
            RC::getPlugin("menu")->triggerHandler("afterMenuParams", array($modelForm, &$params, &$urlparams, $xmlParams));
        }


        $this->set('urlparams', $urlparams);
        $this->set('params', $params);
        $this->set('xmlParams', $xmlParams);
        
        $title = $modelForm->id_menu ? "EXP_MENU_ADD_ITEM_FORM_TOLBARTITLEEDIT" : "EXP_MENU_ADD_ITEM_FORM_TOLBARTITLEADD";

        RC::app()->breadcrumbs = ['label'=>'EXP_MENU_TITLE_ITEM', 'url'=>['/admin/menu/groupmenu']];
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu', {action:'saveClose'})"
                ]
            ]
        ]);
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "VISIBLE" => $modelForm->id_menu ? $this->_access->roles("menu", "EDIT_ITEM") : false,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu', {action:'copy'})",
            "MENU" => [                
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECOPY_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu', {action:'saveCopy'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#menu-form-groupmenu', {action:'copy'})"
                ]
            ]
        ]);
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close', 'id_group'=>$modelForm->id_group]],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
  
    }

    /**
     * Поиск параметров конфигурации пункта меню для типа expansion
     * @return null|XMLConfig
     */
    public function findParams() {
        $menu = maze\menu\MenuSite::instance();
        $modelForm = $this->get('modelForm');
        if ($modelForm->component && $modelForm->view && $modelForm->layout) {
            $views = $menu->views;
            if (isset($views[$modelForm->component][$modelForm->view])) {
                $view = $menu->views[$modelForm->component][$modelForm->view];
                if ($layout = $view->getLayout($modelForm->layout)) {
                    return $layout->params;
                }
            }
        }
        return null;
    }
}

?>