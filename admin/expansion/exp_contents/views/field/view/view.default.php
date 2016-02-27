<?php

defined('_CHECK_') or die("Access denied");

use maze\table\ContentType;
use maze\fields\FieldHelper;

class Contents_View_Field extends View {

    public function registry() {

        $toolbar = RC::app()->toolbar;
        $menu = [];

        RC::app()->breadcrumbs = ['label' => 'EXP_CONTENTS_FIELD', 'url' => ['/admin/contents/field']];
        $bundle = $this->get('bundle');
        if ($bundle) {
            $type = ContentType::find()->where(['expansion' => 'contents', 'bundle' => $bundle])->one();
            if ($type) {
                RC::app()->breadcrumbs = ['label' => $type->title];
            }
            
            $fileds = FieldHelper::listAllFieds(['locked'=>0, 'group'=>'all, contents']);

            foreach ($fileds as $name => $field) {
                $menu[] = [
                    "class" => 'ContextMenu',
                    "TITLE" => $field,
                    "HREF" => ['/admin/contents/field', ['run' => 'add', 'name' => $name, 'bundle' => $bundle]],
                    "ACTION" => "this.href"
                ];
            }

            $toolbar->addGroup('contents', [
                'class' => 'Buttonset',
                "TITLE" => "EXP_CONTENTS_FIELD_ADD",
                "TYPE" => Buttonset::BTNBIG,
                "SORT" => 10,
                "VISIBLE" => true,
                "SORTGROUP" => 10,
                "SRC" => "/library/jquery/toolbarsite/images/icon-plus.png",
                "MENU" => $menu
            ]);

            $toolbar->addGroup('contentsedit', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => true,
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish', 'bundle'=>$bundle]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-field-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish', 'bundle'=>$bundle]],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-field-grid', this.href)"
                ]
            ]
        ]);

            //$this->_access->roles("menu", "DELET_CONTENTS_TYPE")
            $toolbar->addGroup('contents', [
                'class' => 'Buttonset',
                "TITLE" => "EXP_CONTENTS_FIELD_DELETE",
                "TYPE" => Buttonset::BTNBIG,
                "SORT" => 7,
                "VISIBLE" => true,
                "HREF" => [['run' => 'delete', 'bundle'=>$bundle]],
                "SORTGROUP" => 10,
                "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
                "ACTION" => "return cms.btnGridActionPromt('#contents-field-grid', this.href)",
            ]);

            
        }
    }

}

?>