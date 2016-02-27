<?php

defined('_CHECK_') or die("Access denied");

class Menu_View_Groupmenu extends View {

    public function registry() {

        $menu = maze\table\MenuGroup::find()->orderBy('ordering')->all();
        $model = $this->model('Menu'); 
        
        foreach($menu as $item)
        {
             RC::app()->menu->addItems('menu-menu-3', [
            'id'=>'menu-menu-items'.$item->id_group, 
            'title'=>$item->name, 
            'path'=>[['run'=>'menu',  'id_group' => $item->id_group]],
            'active'=>($this->get('id_group') == $item->id_group)
            ]); 
             if($this->get('id_group') == $item->id_group)
             {
                 RC::app()->breadcrumbs = ['label' => $item->name];
             }
            
        }
        $this->set('model', $model);
        $tableId = $this->get('tableId');
        RC::app()->breadcrumbs = ['label' => 'EXP_MENU_VIEW_GROUP_TITLE'];
        $toolbar = RC::app()->toolbar;
        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_ADD_ITEM",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 10,
            "HREF" => [['run' => 'add', 'id_group'=>$this->get('id_group')]],
            "VISIBLE" =>$this->_access->roles("menu", "EDIT_ITEM"),
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/big-add-doc.png",
            "ACTION" => "this.href",
        ]);

        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_TITLEMENU_COPY",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 8,
            "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
            "HREF" => [['run' => 'copy']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-copy.png",
            "ACTION" => "return cms.btnGridAction('#$tableId', this.href)",
        ]);
        
        $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PUBLISH",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
            "MENU" => [
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_PUBLISH_BUTTON',
                    "SORT" => 2,
                    "HREF" => [['run' => 'publish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-unlock.png",
                    "ACTION" => "return cms.btnGridAction('#$tableId', this.href)"
                ],
                [
                    "class" => 'ContextMenu',
                    "TITLE" => 'LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON',
                    "SORT" => 1,
                    "HREF" => [['run' => 'unpublish']],
                    "SRC" => "/library/jquery/toolbarsite/images/icon-lock.png",
                    "ACTION" => "return cms.btnGridAction('#$tableId', this.href)"
                ]
            ]
        ]);
        
        $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 6,
            "HREF" => [['run' => 'pack']],
            "VISIBLE" => $this->_access->roles("menu", "EDIT_ITEM"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
            "ACTION" =>"return cms.btnGridHandler('#$tableId', this.href, {title:'".Text::_('LIB_USERINTERFACE_TOOLBAR_PACK_BUTTON')."'})"
        ]);
        
        $toolbar->addGroup('items', [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_MOVING_BUTTON",
            "TYPE" => Buttonset::BTNMIN,
            "SORT" => 6,
            "HREF" => [['run' => 'moving']],
            "VISIBLE" => $this->_access->roles("menu", "DELET_ITEM"),
            "SORTGROUP" => 5,
            "SRC" => "/library/jquery/toolbarsite/images/asinhron.png",
            "ACTION" => "return cms.btnGridHandler('#$tableId', this.href, {title:'".Text::_('LIB_USERINTERFACE_TOOLBAR_MOVING_BUTTON')."'})"
        ]);

        $toolbar->addGroup('menu', [
            'class' => 'Buttonset',
            "TITLE" => "EXP_MENU_TITLEMENU_DEL_ITEM",
            "TYPE" => Buttonset::BTNBIG,
            "SORT" => 7,
            "VISIBLE" => $this->_access->roles("menu", "DELET_ITEM"),
            "HREF" => [['run' => 'delete']],
            "SORTGROUP" => 10,
            "SRC" => "/library/jquery/toolbarsite/images/icon-trash-big.png",
            "ACTION" => "return cms.btnGridActionPromt('#$tableId', this.href)"
        ]);

    }
}

?>