<?php

defined('_CHECK_') or die("Access denied");

class Languages_View_Overload extends View {

    public function registry() {
      

         $title = $this->get('modelForm')->id ? "EXP_LANGUAGES_OVERLOAD_FORM_TITLE_EDIT" : "EXP_LANGUAGES_OVERLOAD_FORM_TITLE_NEW";
        
        RC::app()->breadcrumbs = ['label' => 'EXP_LANGUAGES_OVERLOAD_NAME', 'url'=>['/admin/languages/overload']];
        RC::app()->breadcrumbs = ['label' => $title];
        $toolbar = RC::app()->toolbar;
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#languages-overload-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#languages-overload-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#languages-overload-form', {action:'saveClose'})"
                ]
            ]
        ]);
        
        $toolbar->addGroup('lang', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "SORTGROUP" => 10,
            "HREF" => [['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);


    }

}

?>