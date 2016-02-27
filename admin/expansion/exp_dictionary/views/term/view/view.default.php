<?php

defined('_CHECK_') or die("Access denied");

use maze\table\ContentType;

class Dictionary_View_Term extends View {

    public function registry() {

        $toolbar = RC::app()->toolbar;

        $id = 'dictionary-term-grid';
        if ($bundle = $this->get('bundle')) {
            RC::app()->breadcrumbs = ['url' => ['/admin/dictionary/term'], 'label' => 'EXP_DICTIONARY_TERM'];
            $type = ContentType::find()->where(['expansion' => 'dictionary', 'bundle' => $bundle])->one();
            RC::app()->breadcrumbs = ['label' => $type->title];
            $id = 'dictionary-term-grid-tree';
        } else {
            RC::app()->breadcrumbs = ['label' => 'EXP_DICTIONARY_TERM'];
        }


        $types = ContentType::find()->where(['expansion' => 'dictionary'])->all();
        $menu = [];

        foreach ($types as $type) {
            $title = $type->title;
            if (!empty($type->description)) {
                $title .= '<span class="menu-help">' . $type->description . '</span>';
            }
            $menu[] = [
                "class" => 'ContextMenu',
                "TITLE" => $title,
                "HREF" => [['run' => 'add', 'bundle' => $type->bundle]],
                "ACTION" => "this.href"
            ];
        }
        if (!empty($menu)) {
            $toolbar->addGroup('terms', [
                'class' => 'Buttonset',
                "TITLE" => "EXP_DICTIONARY_TERM_ADD",
                "TYPE" => Buttonset::BTNBIG,
                "SORT" => 10,
                "VISIBLE" => $this->_access->roles("dictionary", "EDIT_TERM"),
                "SORTGROUP" => 10,
                "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
                "MENU" => $menu
            ]);
        }


        $toolbar->addGroup('terms', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_DICTIONARY_TERM_DELETE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("dictionary", "DELETE_TERM"),
            "HREF" => [['run' => 'delete', 'bundle' => $bundle]],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#$id', this.href)",
        ]);

        $toolbar->addGroup('termsedit', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => $this->_access->roles("dictionary", "EDIT_TERM"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish', 'bundle' => $bundle]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#$id', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish', 'bundle' => $bundle]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#$id', this.href)"
                ]
            ]
        ]);
    }

}

?>