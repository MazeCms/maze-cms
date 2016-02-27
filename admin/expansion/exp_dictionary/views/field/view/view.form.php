<?php

defined('_CHECK_') or die("Access denied");
use maze\fields\FieldHelper;

class Dictionary_View_Field extends View {

    public function registry() {


        $title = $this->get('field')->isNew ? "EXP_DICTIONARY_FIELD_ADD" : "EXP_DICTIONARY_FIELD_EDIT";

        $type = maze\table\ContentType::findOne(['bundle'=>$this->get('field')->bundle, 'expansion'=>'dictionary']);
        RC::app()->breadcrumbs = ['label' => 'EXP_DICTIONARY_FIELD', 'url' => ['/admin/dictionary/field']];
        RC::app()->breadcrumbs = ['label' => $type->title, 'url' => [['run'=>'field', 'bundle'=>$this->get('field')->bundle]]];
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
            "ACTION" => "return cms.btnFormAction('#dictionary-form-field')",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "return cms.btnFormAction('#dictionary-form-field')"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "return cms.btnFormAction('#dictionary-form-field', {action:'saveClose'})"
                ]
            ]
        ]);
       
        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 10,
            "HREF"=>[['run' => 'close', 'bundle'=>$this->get('field')->bundle]],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);

        $this->set('widgets', FieldHelper::listFieldWidget($this->get('name')));

    }

}

?>