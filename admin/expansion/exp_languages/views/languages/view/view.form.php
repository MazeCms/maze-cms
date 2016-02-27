<?php

defined('_CHECK_') or die("Access denied");

class Languages_View_Languages extends View {

    public function registry() {
        $title = $this->get('modelForm')->id_lang ? Text::_("EXP_LANGUAGES_FORM_TITLEEDIT") : Text::_("EXP_LANGUAGES_FORM_TITLENEW");
        
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
            "ACTION" => "return cms.btnFormAction('#languages-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#languages-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#languages-form', {action:'saveClose'})"
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