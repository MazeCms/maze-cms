<?php

defined('_CHECK_') or die("Access denied");

use maze\table\ContentType;

class Contents_View_Contents extends View {

    public function registry() {

        $toolbar = RC::app()->toolbar;

        $types = ContentType::find()->where(['expansion' => 'contents'])->all();

        $menu = [];
        foreach ($types as $type) {
            $title = $type->title;
            if(!empty($type->description)){
               $title .= '<span class="menu-help">'.$type->description.'</span>';
            }
            $menu[] = [
                "class" => 'ContextMenu',
                "TITLE" => $title,
                "HREF" => [['run' => 'add', 'bundle' => $type->bundle]],
                "ACTION" => "this.href"
            ];
        }
        if($menu){
            $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_CONTENTS_ADD",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "VISIBLE" => ($this->_access->roles("contents", "EDIT_CONTENTS") || $this->_access->roles("contents", "EDIT_SELF_CONTENTS")),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "MENU" => $menu
        ]);
        }
        

        $toolbar->addGroup('contents', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_DELETE_TYPE",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" =>($this->_access->roles("contents", "DELETE_CONTENTS") || $this->_access->roles("contents", "DELETE_SELF_CONTENTS")),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#contents-grid', this.href)",
        ]);
        
        $toolbar->addGroup('contentsedit', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => ($this->_access->roles("contents", "EDIT_CONTENTS") || $this->_access->roles("contents", "EDIT_SELF_CONTENTS")),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#contents-grid', this.href)"
                ]
            ]
        ]);
        
        $toolbar->addGroup('contentsedit', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_CONTENTS_CONT_BTNHOME",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 10,
            "VISIBLE" => ($this->_access->roles("contents", "EDIT_CONTENTS") || $this->_access->roles("contents", "EDIT_SELF_CONTENTS")),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-home.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_CONTENTS_CONT_BTNHOMEIN',
                    "SORT" => 2,
                    "HREF" => [['run' => 'home']],
                    "ACTION" => "return cms.btnGridAction('#contents-grid', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'EXP_CONTENTS_CONT_BTNHOMEOUT',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unhome']],
                    "ACTION" => "return cms.btnGridAction('#contents-grid', this.href)"
                ]
            ]
        ]);
    }

}

?>