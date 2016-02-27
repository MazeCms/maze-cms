<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppMenu
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */

namespace maze\conf;

use maze\helpers\Html;

class AppMenu {

    protected static $count = 1;
    protected $access;
    protected $app;
    protected $menu;
    protected $items = [];
    protected $active = false;
    protected $curent;

    public function __construct() {
        $this->access = \Access::instance();
        $this->curent = \URI::instance()->toString(['path', 'query', 'fragment']);
    }

    public function loadApp() {
        if ($this->app == null) {
            $this->app = \RC::getDb()->cache(function($db){
                return \maze\table\Expansion::find()
                    ->innerJoinWith('installApp.groupExp')
                    ->andOnCondition(['ia.front_back' => 0])
                    ->orderBy('ge.ordering, ia.ordering')                    
                    ->all();
            }, null, 'fw_appmenu');
        }

        return $this->app;
    }

    public function createMenu() {
        
        if ($this->menu !== null)
            return $this->menu;
        $result = array();
        $application = $this->loadApp();

        
        foreach ($application as $app) {

            if (!$this->access->roles($app->name, "VIEW_ADMIN"))
                continue;

            $info = \RC::getConf(["type" => "expansion", "name" => $app->name]);

            if (!$info->getMenu())
                continue;

            $menu = $info->getMenu();
            static::$count = 1;
            $menu['help'] = '<div><strong>' . \Text::_('LIB_USERINTERFACE_TOOLBAR_DES') . '</strong>: ' . $info->get('description') . '</div>';
            $menu['help'] .= '<div><strong>' . \Text::_('LIB_USERINTERFACE_TOOLBAR_AUTHOR') . '</strong>: ' . $info->get('author') . '</div>';
            $menu['help'] .= '<div><strong>' . \Text::_('LIB_USERINTERFACE_TOOLBAR_VERSION') . '</strong>: ' . $info->get('version') . '</div>';
            $menu['help'] .= '<div><strong>' . \Text::_('LIB_USERINTERFACE_TOOLBAR_SITE') . '</strong>: ' . $info->get('siteauthor') . '</div>';
            $result[$app->name] = $this->builder($menu, $app->name);
        }
       
        $this->menu = $result;
        return $this->menu;
    }

    protected function builder($menu, $appName, $root = true) {
        if(isset($menu['path'])){
            $url = \Route::_(str_replace('{{AND}}', '&', (string) $menu['path']));
        }else{
            $url = null;
        }
        
 
        $img = '';
        if(isset($menu['img'])){
          $img = (string)$menu['img'];
          $pathImg = \RC::getAlias('@root/'.trim((string)$menu['img'], '\/'));
          if(!empty($menu['img']) && file_exists($pathImg)){
            $img = Html::pathThumb($pathImg, 16, 16);
          }
        }
        
        $result = [
            'id' => (isset($menu['id']) ? (string)$menu['id'] : 'menu-' . $appName . '-' . static::$count++),
            'img' => $img ,
            'title' => \Text::_($menu['title']),
            'path' => $url,
            'active' =>(isset($menu['active']) ? $menu['active'] : ($url == $this->curent && !$this->active) ),
            'visible' => true
        ];
        if($result['active']) $this->active = true;
        
        if (isset($menu['help'])) {
            $result['help'] =(string)$menu['help'];
        }
        if (isset($menu['access'])) {
            $ac = explode(':', $menu['access']);
            if (count($ac) == 2) {
                $result['visible'] = $this->access->roles(trim($ac[0]), trim($ac[1]));
            }
        }

        if (isset($menu['onclick'])) {
            $result['onclick'] = $menu['onclick'];
        }

        if (isset($menu->item)) {
            $result['item'] = [];
            foreach ($menu->item as $m) {
                $result['item'][] = $this->builder($m, $appName, false);
            }
        }
        
        if (isset($this->items[$result['id']])) {
           if(!isset($result['item']))
           {
               $result['item'] = [];
           }
           foreach($this->items[$result['id']] as $child){
              $result['item'][] = $this->builder($child, $appName, false); 
           }
           // array_merge($result['item'], $this->items[$result['id']]);
         
        }
        
        return $result;
    }

    public function addItems($parent, $items) {
        if (!isset($this->items[$parent])) {
            $this->items[$parent] = [];
        }


        $this->filterItems($items);
        
        if (!empty($items)) {
            $this->items[$parent][] = $items;
        }
    }


    protected function filterItems(&$items) {
        if (isset($items['id']) && isset($items['title']) && isset($items['path'])) {
             
            $items = array_merge(['img' => '', 'active' => false, 'visible' => true], $items);
            $items['path'] = \Route::_($items['path']);
            $items['active'] = $items['active'] && !$this->active ?  true : ($items['path'] == $this->curent && !$this->active);
            if($items['active']) $this->active = true;
            if (isset($items['item'])) {
                foreach ($items['item'] as $key => $item) {
                    $this->filterItems($items['item'][$key]);
                }
            }
        } else {
            unset($items);
        }
    }

}
