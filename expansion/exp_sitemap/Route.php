<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author nick
 */

namespace root\expansion\exp_sitemap;

use RC;
use maze\helpers\ArrayHelper;
use maze\table\Routes;
use admin\expansion\exp_sitemap\table\Sitemap;

/**
 * Создание маршрута карты сайта
 * 
 * 
 */
class Route extends \maze\url\RouteApp {

    protected $map;
    
    protected $aliasMap;

    /**
     * Разбор маршрута
     * 
     * @return boolean|array
     */
    public function parseRoute() {
        $parsePath = explode('/', $this->path);
        $target = end($parsePath);
        $parsePathBase = count($parsePath) > 1 ? array_slice($parsePath, 0, -1) : null;
        $pathBase = $parsePathBase ? implode('/', $parsePathBase) : null;
        $maps = $this->getAliasMaps();


        // определяем цели
        if (isset($maps[$target])) {
            if ($pathBase) {
                if ($itemsMenu = $this->findPathMenuByAlias($pathBase)) {
                    if (defined("SITE"))
                        RC::app()->breadcrumbsArr = $itemsMenu->getBreadcrumbs();
                }
            }

            if (defined("SITE"))
                RC::app()->breadcrumbs = ['label' => $maps[$target]->title];
            $url = \URI::instance();
            $path = $url->getPath();
            if(preg_match('/[^\.]+.xml$/', $path)){
                $this->router->setRun('xml');
            }
            return ['/controller/sitemap/default', ['sitemap_id' => $maps[$target]->sitemap_id]];
        }
    }

    /**
     * Создание маршрута 
     * 
     * @param URI $path - текущий  оригинальный (/компонент/контроллер/вид/шаблон, параметры) URL
     * @param array $params - дополнительные не обязательные параметры 
     * @return string|boolean - путь к цели
     */
    public function createRoute($path, $params) {


        $pathUrl = $path->getPath();
        $pathParse = explode('/', trim($pathUrl, '/'));

        if (isset($pathParse[0])) {
            $component = $pathParse[0];
        }
        if (isset($pathParse[1])) {
            $controler = $pathParse[1];
        }
        if (isset($pathParse[2])) {
            $view = $pathParse[2];
        }
        if (isset($pathParse[3])) {
            $layout = $pathParse[3];
        }

        $menu = RC::getMenu();

        // маршрут для карты сайта
        if (isset($controler) && $controler == 'controller') {
            $sitemap_id = $path->getVar('sitemap_id');
            $maps = $this->getMaps();

            if (isset($maps[$sitemap_id])) {
                return $maps[$sitemap_id]->route->alias;
            }
        }
    }


    public function findPathMenuByAlias($path) {
        $menu = RC::getMenu();
        if (($itemMenu = $menu->findAlias($path))) {
            return $itemMenu;
        }
        return false;
    }

    protected function getMaps() {
        if ($this->map === null) {
            $this->map = RC::getDb()->cache(function($db) {
                return Sitemap::find()
                                ->indexBy('sitemap_id')
                                ->from(['m' => Sitemap::tableName()])
                                ->joinWith(['route'])->groupBy('m.sitemap_id')
                                ->all();
            }, null, 'exp_sitemap');
        }
        return $this->map;
    }

    public function getAliasMaps() {
        if ($this->aliasMap === null) {
            $contents = $this->getMaps();
            foreach ($contents as $cont) {
                $this->aliasMap[$cont->route->alias] = $cont;
            }
        }
        return $this->aliasMap;
    }

}
