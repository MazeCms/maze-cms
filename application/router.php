<?php

defined('_CHECK_') or die("Access denied");

use maze\exception\NotFoundHttpException;

class SiteRouter extends Router {

    /**
     * @var maze\menu\Item|null - текущий активный пункт меню  
     */
    public $_menu;

    /**
     * @var array  routeApp - коллекция объектов маршрутов расширений
     */
    protected $routeApp = [];
    protected $routreAppParse = [];

    public function getMenu() {
        return $this->_menu;
    }

    /**
     * ДИСПЕТЧЕР МАРШРУТОВ
     * 
     * @return NotFoundHttpException 
     */
    public function dispatcher() {

        $url = $this->getUrlparse();
        $menu = RC::getMenu();

        if ($this->_menu == null) {
            if ($this->getIsHome()) {
                if (($home = $menu->getHome())) {
                    $this->_menu = $home;
                    if (!$this->setExp($this->_menu->getComponent())) {
                        throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name' => $this->_menu->getComponent()]));
                    }
                }
            }
        }


        if (!$this->getIsHome()) {
            $this->_menu = $menu->findAlias($this->getUrlPath());
        }


        if ($this->_menu) {
            if ($this->_menu->isExp) {
                if (!$this->setExp($this->_menu->getComponent())) {
                    throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name' => $this->_menu->getComponent()]));
                }

                if ($this->_menu->getController()) {
                    $this->setController($this->_menu->getController());
                }

                if ($this->_menu->getView()) {
                    $this->setView($this->_menu->getView());
                }

                if ($this->_menu->getLayout()) {
                    $this->setLayout($this->_menu->getLayout());
                }

                if ($urlParam = $this->_menu->getParams()) {
                    foreach ($urlParam as $name => $val) {
                        $this->getRequest()->setGet($name, $val);
                    }
                }
                if (!$this->getIsHome()) {
                    RC::app()->breadcrumbsArr = $this->_menu->getBreadcrumbs();
                }
            } elseif ($this->_menu->isAlias) {
                $url = new URI($this->_menu->paramLink);
                $path = trim($url->getPath(), '/');
                $alias = explode('/', $path);

                if (!isset($alias[0]) || empty($alias[0]) || !$this->setExp($alias[0])) {
                    throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name' => $alias[0]]));
                }

                if ($url->hasVar('run')) {
                    $this->setRun($url->getVar('run'));
                }

                if ($vars = $url->getQuery(true)) {
                    foreach ($vars as $name => $val) {
                        $this->getRequest()->setGet($name, $val);
                    }
                }
                RC::app()->breadcrumbsArr = $this->_menu->getBreadcrumbs();
                $this->getRoutes($path);
            } elseif ($this->_menu->isUrl) {
                return RC::app()->document->setRedirect($this->_menu->paramLink, 301);
            }
        } else {

            if ($routes = $this->getRoutesApp()) {
                $target = null;
                $appName = null;
                foreach ($routes as $name => $route) {
                    $route->path = $this->getUrlPath();
                    if ($target = $route->parseRoute()) {
                        $appName = $name;
                        break;
                    }
                }

                if ($appName && $target && is_array($target)) {

                    $pathTarget = explode('/', trim($target[0], '/'));

                    if (!$this->setExp($appName)) {
                        throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name' => $appName]));
                    }

                    if (isset($pathTarget[0])) {
                        $this->setController($pathTarget[0]);
                    }

                    if (isset($pathTarget[1])) {
                        $this->setView($pathTarget[1]);
                    }

                    if (isset($pathTarget[2])) {
                        $this->setLayout($pathTarget[2]);
                    }

                    if (isset($target[1])) {
                        foreach ($target[1] as $name => $val) {
                            $this->getRequest()->setGet($name, $val);
                        }
                    }

                    return true;
                }
            }
            $link = count($url) > 1 ? $url[0] : $url[0];

            if (empty($link)) {
                throw new NotFoundHttpException(Text::_('Неизвестный формат запроса'));
            }

            if (!$this->setExp($link)) {
                throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name' => $link]));
            }

            $this->getRoutes();
        }
    }

    public function getRoutesApp() {
        if (empty($this->routeApp)) {
            $apps = RC::getDb()->cache(function($db) {
                return maze\table\Expansion::find()
                                ->innerJoinWith('installApp')
                                ->andOnCondition(['ia.front_back' => 1])
                                ->orderBy('ia.ordering')
                                ->all();
            }, null, 'fw_route');
            foreach ($apps as $app) {
                $this->getRouteApp($app->name);
            }
        }

        return $this->routeApp;
    }

    protected function createRouteApp($name) {
        $className = 'root\expansion\exp_' . $name . '\Route';

        if (file_exists(RC::getAlias('@root/expansion/exp_' . $name . '/Route.php'))) {
            return $this->routeApp[$name] = RC::createObject(['class' => $className, 'router' => $this, 'path' => $this->getUrlPath()]);
        }
        return null;
    }

    public function getRouteApp($name) {

        $route = null;
        if (isset($this->routeApp[$name])) {
            $route = $this->routeApp[$name];
        } else {
            $route = $this->createRouteApp($name);
        }
        return $route;
    }


    public function parseRouteApp($path) {
        $uri = new URI($path);
        $pathUri = trim($uri->getPath(), '/');
        $result = null;
        if ($pathUri) {
           $routes = $this->getRoutesApp();
            foreach($routes as $name=>$r){
                $r->path = $pathUri;
                if($result = $r->parseRoute()){
                    $result[0] = '/'.$name.$result[0];
                    break;
                }
            }
           
        }

        return $result;
    }

    public function createRoute($path, $params = []) {

        $menu = RC::getMenu();

        if ($itemMenu = $menu->findPath($path)) {
            $path = $itemMenu->getPath();
        } else {
            $uri = new URI($path);
            $pathUri = trim($uri->getPath(), '/');
            $alias = explode('/', $pathUri);
            if (isset($alias[0])) {
                if ($router = $this->getRouteApp($alias[0])) {
                    if (($url = $router->createRoute($uri, $params))) {
                        $path = $url;
                    }
                }
            }
        }

        return $path;
    }

}

?>