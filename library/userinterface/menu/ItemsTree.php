<?php

namespace ui\menu;

use ui\menu\SelectTree;
use maze\table\MenuGroup;

class ItemsTree extends SelectTree {

    public function init() {
       
       $this->items = [];       
       $menu = MenuGroup::find()->joinWith(['items'=>function($query){ $query->orderBy('ordering');}])->all();

       foreach($menu as $m)
       {
           $this->items[] = ['label' => $m->name, 'data-icon'=>'/library/image/icons/blue-folder-network-horizontal-open.png', 'value' =>$m->id_group.'-menu', 'parent' =>0, 'disabled' =>true];
           foreach($m->items as $item){
               $this->items[] = [
                   'label' => $item->name, 
                   'value' =>$item->id_menu,
                   'data-icon'=>'/library/image/icons/'.($item->enabled ? 'blue-folder-horizontal.png' : 'lock-warning.png'),
                   'parent' =>($item->parent > 0 ? $item->parent : $m->id_group.'-menu')];
           }
       }

        parent::init();
    }

}
