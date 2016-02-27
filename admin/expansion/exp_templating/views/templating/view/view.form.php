<?php

defined('_CHECK_') or die("Access denied");
use  maze\table\InstallApp;

class Templating_View_Templating extends View {

    public function registry() {

       $params = null;
       $xmlParams = null;
        if($modelForm = $this->get('modelForm')){
            if($modelForm->name){
                $appTmp = InstallApp::find()->where(['type'=>'template', 'name'=>$modelForm->name])->one();
                $modelForm->front = $appTmp->front_back;
                $xmlParams = RC::getConf(["name" =>$modelForm->name, "type" => "template", "front" =>$modelForm->front], $modelForm->param);
                $params = $xmlParams->getParams();
            }
        }
        
        $this->set("params", $params);
        $this->set("xmlParams", $xmlParams);

//     RC::getPlugin("template")->triggerHandler("styleParams", array(&$conf, $front));
        $title = $modelForm->id_tmp > 0 ? $title = Text::_("EXP_TEMPLATING_STYLE_FORM_TITLE_EDIT") : Text::_("EXP_TEMPLATING_STYLE_FORM_TITLE_CREATE");
        RC::app()->breadcrumbs = ['label'=>'EXP_TEMPLATING_SUB_STYLE', 'url'=>['/admin/templating']];
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('style', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#templating-style-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#templating-style-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#templating-style-form', {action:'saveClose'})"
                ]
            ]
        ]);
        $toolbar->addGroup('style', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 9,
            "VISIBLE" => $modelForm->id_tmp !== null,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnFormAction('#templating-style-form', {action:'copy'})",
            "MENU" => [                
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECOPY_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#templating-style-form', {action:'saveCopy'})"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_COPY_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#templating-style-form', {action:'copy'})"
                ]
            ]
        ]);
        $toolbar->addGroup('style', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
  
    }



}

?>