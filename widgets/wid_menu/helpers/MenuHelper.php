<?php

namespace wid\wid_menu\helpers;

use RC;

class MenuHelper {

    /**
     * Отсортировать пункты по родителям
     * 
     * @param array|maze\menu\Item $items
     * @return array|maze\menu\Item
     */
    public static function getItemsMenu($items) {
        $sort = [];

        foreach ($items as $item) {
            $sort[$item->parent][] = $item;
        }
        return $sort;
    }

    public static function getItems($items, $params, $id) {
        $itemsParent = static::getItemsMenu($items);
        $activeId = static::getIDActive($items);
        $activeParentID = static::getIDsParents($items);
        $parentStart = $params->getVar('parent_id') ? $params->getVar('parent_id') : 0;
        $result = [];
        if(!isset($itemsParent[$parentStart])) return [];
        
        foreach ($itemsParent[$parentStart] as $item) {
            $attr_li = [];
            $attr_a = [];
            $active = false;
            $itemCurent = [];
            if ($activeParentID && in_array($item->id_menu, $activeParentID)) {
                $attr_li['class'] = $params->getVar("active_parent");
                $active = true;
                $attr_li['active_parent'] = true;
            } elseif ($activeId && $activeId == $item->id_menu) {
                $attr_li['class'] = $params->getVar("active_target");
                $active = true;
                $attr_li['active_target'] = true;
            }
            if ($item->get('menu_css_class')) {
                $attr_li['class'] = isset($attr_li['class']) ? ' ' . $item->get('menu_css_class') : $item->get('menu_css_class');
            }
            
            $itemCurent['attr_li'] = $attr_li;
            $itemCurent['attr_a'] = ['id' => 'menu-' . $id . '-items-' . $item->id_menu];
            $itemCurent['item'] = $item;
            $itemCurent['level'] = 0;
            $showSubmenu = $params->getVar('subitem') ? true : $active;
            if ($showSubmenu && isset($itemsParent[$item->id_menu])) {

                $level = 0;
                 $itemCurent['items'] =  static::childrenItems($itemsParent, $item->id_menu, $activeParentID, $activeId, $params, $id, $level);
            }
            $result[] = $itemCurent;
        }
        return $result;
    }
    
    public static function childrenItems($itemsParent, $parentStart, $activeParentID, $activeId, $params, $id, $level){

        $result = [];
        $level++;
     
        if($params->getVar('level') != 0 && $level >= $params->getVar('level')){
            return false;
        }
        
        foreach ($itemsParent[$parentStart] as $item) {

            $attr_li = [];
            $active = false;
            $itemCurent = [];
            if ($activeParentID && in_array($item->id_menu, $activeParentID)) {
                $attr_li['class'] = $params->getVar("active_parent");
                $active = true;
                $attr_li['active_parent'] = true;
            } elseif ($activeId && $activeId == $item->id_menu) {
                $attr_li['class'] = $params->getVar("active_target");
                $attr_li['active_target'] = true;
                $active = true;
            }
            if($item->get('menu_css_class')){
                $attr_li['class'] = isset($attr_li['class']) ? ' '.$item->get('menu_css_class') : $item->get('menu_css_class');
            }
            $itemCurent['level'] = $level;
            $itemCurent['attr_li'] = $attr_li;
            $itemCurent['attr_a'] = ['id' => 'menu-' . $id . '-items-' . $item->id_menu];
            
            $itemCurent['item'] = $item;
            $showSubmenu = $params->getVar('subitem') ? true : $active;
           
            if ($showSubmenu && isset($itemsParent[$item->id_menu])) {
                $itemCurent['items'] = static::childrenItems($itemsParent, $item->id_menu, $activeParentID, $activeId, $params, $id, $level);
            }
            
            $result[] = $itemCurent;
        }
        return $result;

    }

    /**
     * ID Текущий активный нукты меню
     * 
     * @param type $items
     * @return array
     */
    public static function getIDActive($items) {

        $result = null;
        if (RC::app()->router->getIsHome()) {
            if ($item = RC::getMenu()->getHome()) {
                $result = $item->id_menu;
            }

            return $result;
        }
        $path = RC::app()->router->getUrlPath(); 
        
        
        foreach($items as $item){
            if($path == $item->getPath()){
                $result = $item->id_menu;
                break;
            }
            
        }
       

        return $result;
    }

    /**
     * ID родитеских активных пуктов меню
     * 
     * @param type $items
     * @return array
     */
    public static function getIDsParents($items) {
        $result = [];
        $parent = null;
        if (RC::app()->router->getIsHome()) {
            return $result;
        }
        
        if (!($parent = static::getIDActive($items))) {
            $path = RC::app()->router->getUrlPath();             
            $pathParse = explode("/", $path);
           
            for ($i=0, $c=count($pathParse); $i < count($pathParse); $i++, $c--) {
                $targetPath =implode("/",array_slice($pathParse, 0, $c));
                foreach($items as $item){                    
                    if($targetPath == $item->getPath()){
                        $result[] = $item->id_menu;
                        break;
                    }
                }
            }
            
           
        } else {
           
            $parent = RC::getMenu()->getItemByID($parent);
            if ($parent) {
                $parent = $parent->parent;
            }
        }

        if ($parent) {
            while ($item = RC::getMenu()->getItemByID($parent)) {
                $result[] = $item->id_menu;
                $parent = $item->parent;
            }
        }
      
           
        return $result;
    }

}
