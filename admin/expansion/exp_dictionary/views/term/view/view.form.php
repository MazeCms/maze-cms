<?php

defined('_CHECK_') or die("Access denied");

class Dictionary_View_Term extends View {

    public function registry() {


        $model = $this->get('model');
        $title = $model->id ? "EXP_DICTIONARY_EDIT" :  "EXP_DICTIONARY_ADD" ;
        RC::app()->breadcrumbs = ['url'=>['/admin/dictionary/term'], 'label'=>'EXP_DICTIONARY_TERM'];
        RC::app()->breadcrumbs = ['url'=>['/admin/dictionary/term', ['run'=>'term', 'bundle'=>$model->bundle]], 'label'=>$model->type->type->title];
        RC::app()->breadcrumbs = ['label'=>$title];
        $toolbar = RC::app()->toolbar;

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true, //$this->_access->roles("menu", "EDIT_MENU"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "return cms.btnFormAction('#dictionary-term-form')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#dictionary-term-form')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#dictionary-term-form', {action:'saveClose'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close', 'bundle'=>$model->bundle]],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);

    }

}

?>