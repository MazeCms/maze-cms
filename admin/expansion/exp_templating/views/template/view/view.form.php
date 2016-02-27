<?php

defined('_CHECK_') or die("Access denied");

class Templating_View_Template extends View {

    public function registry() {

        RC::app()->breadcrumbs = ['label' => 'EXP_TEMPLATING_TMP_TABLE_TITLE', 'url' => ['/admin/templating/template']];
        RC::app()->breadcrumbs = ['label' => $this->get('title')];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-save-floppy.png",
            "ACTION" => "appTemplate.saveFile(); return false;",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON',
                    "SORT" => 2,
                    "ACTION" => "appTemplate.saveFile(); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON',
                    "SORT" => 1,
                    "ACTION" => "appTemplate.saveFile(appTemplate.closeTable); return false;"
                ]
            ]
        ]);
        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFOLDER",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 2,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/big-add-folder.png",
            "ACTION" => "appTemplate.createFile('folder');  return false;"
        ]);
        $toolbar->addGroup('tmp', [
            'class' => 'Buttonset',
            "TITLE" => Text::_("EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFILE", ['type'=>'']),
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>7,
            "SORTGROUP" => 2,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "appTemplate.createFile('php'); return false;",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => Text::_("EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFILE", ['type'=>'php']),
                    "SORT" => 2,
                    "ACTION" => "appTemplate.createFile('php'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => Text::_("EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFILE", ['type'=>'css']),
                    "SORT" => 1,
                    "ACTION" => "appTemplate.createFile('css'); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => Text::_("EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFILE", ['type'=>'ini']),
                    "SORT" => 1,
                    "ACTION" => "appTemplate.createFile('lang'); return false;"
                ]
            ]
        ]);
        
        $toolbar->addGroup('tmpedit', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_TEMPLATING_TMP_FORM_JS_TREE_PASTE",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 4,
            "SRC" => "/library/jquery/toolbarsite/images/icon-clipboard-paste.png",
            "ACTION" => "appTemplate.pasteNode(); return false;",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_TEMPLATING_TITLEMENU_COPY',
                    "SRC" => "/library/jquery/toolbarsite/images/icon-copy-16.png",
                    "SORT" => 3,
                    "ACTION" => "appTemplate.copyNode(); return false;"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_TEMPLATING_TITLEMENU_CUT',
                    "SRC" => "/library/jquery/toolbarsite/images/icon-cuthere.png",
                    "SORT" => 2,
                    "ACTION" => "appTemplate.cutNode(); return false;"
                ]
            ]
        ]);
        $toolbar->addGroup('tmpedit', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_TEMPLATING_TITLEMENU_PASTE",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" =>9,
            "SORTGROUP" => 2,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
            "ACTION" => "appTemplate.deleteNode(); return false;"
        ]);
        $toolbar->addGroup('tmpedit', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_TEMPLATING_TMP_FORM_JS_TREE_RENAME",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" =>1,
            "SORTGROUP" => 1,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/rename-icon-16.png",
            "ACTION" => "appTemplate.rename(); return false;"
        ]);
        $toolbar->addGroup('edit', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 2,
            "HREF"=>[['run' => 'close']],
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-left.png",
            "ACTION" => "return cms.redirect(this.href)"
        ]);
        
        $toolbar->addGroup('edit', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_TEMPLATING_TITLEMENU_VIEW",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" =>8,
            "SORTGROUP" => 1,
            "HREF"=>$this->get('url')->toString(),
            "SRC" => "/library/jquery/toolbarsite/images/icon-arrow-up.png",
            "ACTION" => "window.open(this.href); return false;"
        ]);
        
        $this->_doc->setLangTextScritp([
            'EXP_TEMPLATING_TITLEMENU_EDIT',
            'EXP_TEMPLATING_TMP_PACH_FILE',
            'EXP_TEMPLATING_LOADTEXT',
            'EXP_TEMPLATING_TITLEMENU_CUT',
            'EXP_TEMPLATING_TITLEMENU_COPY',
            'EXP_TEMPLATING_TMP_FORM_MESS_NOFILE',
            'EXP_TEMPLATING_TMP_FORM_MESS_NO_SELECT',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_MULTI',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_NEWFOLDER',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_PASTE',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_CREATEFOLDER',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_RENAME',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_DELETE',
            'EXP_TEMPLATING_TMP_FORM_JS_TREE_VIEW',
            'EXP_TEMPLATING_TMP_FORM_BTN_COPY',
            'EXP_TEMPLATING_TMP_FORM_JS_SAVE',
            'EXP_TEMPLATING_TMP_FORM_JS_SAVECLOSE',
            'EXP_TEMPLATING_TMP_FORM_JS_SAVECOPY',
            'EXP_TEMPLATING_TMP_FORM_JS_COPYNAME_LABEL',
            'EXP_TEMPLATING_TMP_FORM_JS_COPYNAME_DIALOG',
            'EXP_TEMPLATING_TMP_FORM_JS_COPYTMP_DIALOG',
            'EXP_TEMPLATING_TMP_FORM_JS_COPYTMP_EMPTYNAME'
        ]);
    }

}

?>