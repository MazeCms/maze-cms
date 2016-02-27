<?php

defined('_CHECK_') or die("Access denied");

class Widget_View_Widget extends View {

    public function registry() {

        $name = $this->get('name');
        $front = $this->get('front');
        $modelForm = $this->get('modelForm');
        $model = $this->get('model');

        $xmlParams = RC::getConf(array("name" => $name, "type" => "widget", "front" => $front), $modelForm->param);
        $params = $xmlParams->getParams();
        
        $title = $modelForm->id_wid ? Text::_("EXP_WIDGET_FORM_TITLE_EDIT") : Text::_("EXP_WIDGET_FORM_TITLE_ADD");
        $label = $front ? 'EXP_WIDGET_WIDGETS_FILTER_FORNT_SITE' : 'EXP_WIDGET_WIDGETS_FILTER_FORNT_ADMIN';
        RC::app()->breadcrumbs = ['label' =>$label, 'url' => ['admin/widget', ['front_back'=>$front]]];
        RC::app()->breadcrumbs = ['label' => $xmlParams->get('name')];
        RC::app()->breadcrumbs = ['label' => $title];
        $postionWidget = $model->getWidgetPosition($modelForm->id_tmp, $modelForm->position);
        $toolbar = RC::app()->toolbar;
         $toolbar->addGroup('widget', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#widget-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#widget-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#widget-form', {action:'saveClose'})"
                ]
            ]
        ]);
        $toolbar->addGroup('widget', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "VISIBLE" => $modelForm->id_wid !== null,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnFormAction('#widget-form', {action:'copy'})",
            "MENU" => [                
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECOPY_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#widget-form', {action:'saveCopy'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#widget-form', {action:'copy'})"
                ]
            ]
        ]);
        $toolbar->addGroup('widget', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close', 'front'=>$front]],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
  
        
        $this->set('xmlParams', $xmlParams);
        $this->set('params', $params);
        $this->set('postionWidget', $postionWidget);
//      RC::getPlugin("widget")->triggerHandler("widgetParams", array(&$params, $conf, $front, $name));

    }

   

}

?>