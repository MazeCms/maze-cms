<?php

defined('_CHECK_') or die("Access denied");

/*
 * БАЗОВЫЙ АБСТРАКТНЫЙ КЛАСС МАРШРУТИЗАТОРА
 */

use maze\base\Object;

abstract class Router extends Object{

    /**
     * @var array - "копилка" статических маршрутов
     */
    protected $_routes = []; 
    /**
     * @var string - текуший компанент расширения 
     */
    protected $_component;  
    /**
     * @var string - текуший контроллер расширения 
     */
    protected $_controller;  
    /**
     * @var string - текуший метод контроллера расширения 
     */
    protected $_run;    
    /**
     * @var string - текуший вид расширения 
     */
    protected $_view;     
    /**
     * @var string - текуший шаблон вида расширения 
     */
    protected $_layout;   
    /**
     * @var type string имя файла класса вида
     */
    protected $_classView;  
    
    protected $_urlparse;
    /**
     * @var string - текущий путь (маршрут) вида link/links 
     */
    protected $_urlpath;


    protected $_exp;
    
    public function init() {
    }

    /**
     * РАЗБИТЬ ТЕКУЩИЙ URL
     * 
     * return  массив имен url
     */

    public function getUrlparse() {
        if($this->_urlparse == null){
            $url =  $this->getUrlPath();	
            $this->_urlparse = explode("/", $url);
        }
        return $this->_urlparse;
    }
    
    public function getUrlPath(){
        if($this->_urlpath == null){
            $this->_urlpath = URI::instance()->getPath(); 
            $this->_urlpath  = trim(trim(preg_replace(["/^\/?index\.[a-zA-Z-0-9]+/i", "/\.[a-z0-9-_]+$/i"], "", $this->_urlpath),'/'));
        }
       return $this->_urlpath;
    }


    public function getIsHome(){
       $url = $this->getUrlparse();
       return empty($url[0]);
    }


    public function setComponent($value) {
        $this->_component = $value;
    }

    public function setController($value) {
        $this->_controller = $value;
    }

    public function setRun($value) {
        $this->_run = $value;
    }

    public function setView($value) {
        $this->_view = $value;
    }

    public function setLayout($value) {
        $this->_layout = $value;
    }

    public function setClassView($value) {
        $this->_classView = $value;
    }
    
    public function getComponent() {
        if($this->_exp !== null){
            $this->_component = $this->_exp->name;
        }
        return $this->_component;
    }

    public function getController() {
        return $this->_controller;
    }

    public function getRun() {
        return $this->_run;
    }

    public function getView() {
        return $this->_view;
    }

    public function getLayout() {
        return $this->_layout;
    }

    public function getClassView() {
        return $this->_classView;
    }
    
    public function getRequest(){
        return RC::app()->request;
    }
    
    public function getExp(){
        return $this->_exp;
    }
    /**
     * ЗАГРУЗКА ПРИЛОЖЕНИЯ
     * 
     * @param (string) $name - проверямое имя приложения из БД
     * return (bool) возвращает true если приложение установленно, включено и имеется точка входа
     * 
     */

    public function setExp($name) {
        $this->_exp = RC::app()->getComponent($name);
        if(!$this->_exp->is) return false;
        return $this->_exp;
    }

    /**
     * 	НАЗНАЧЕНИЕ СТАТИЧЕСКОГО МАРШРУТА
     * 
     * 	@param  $pattern (string)  - шаблон SEF, разделитель "/"
     * 	@param $param (string)  - парамеры компонента (контроллер/вид/шаблон), разделитель "/",
     * допускается (/вид/) или (контроллер/вид/) или (контроллер) или (контроллер//шаблон)
     */
    public function setRouters($pattern, $param) {
        $this->_routes[$pattern] = $param;
    }

    /**
     * ВЫЗОВ СТАТИЧЕСКОГО МАРШРУТА
     * 
     * если статические маршруты не назначены то url имеет вид : /контроллер/вид/шаблон,
     * или, /контроллер, или /контроллер/вид
     * если статические маршруты назначены то url иоеет вид : произвольный url = $this->$_routes[шаблон] =  
     * параметры (контроллер/вид/шаблон)
     * @var (string) $_GET['run'] - метод текущего котроллера
     */
    public function getRoutes($link = null) {
        
        $urlArr = $link == null ? $this->getUrlparse() : explode('/', $link);

        array_shift($urlArr);
        if (empty($this->_routes)) {
            if (isset($urlArr[0])) {
                $this->_controller = $urlArr[0];
            }
            if (isset($urlArr[1])) {
                $this->_view = $urlArr[1];
            }
            if (isset($urlArr[2])) {
                $this->_layout = $urlArr[2];
            }
            if ($this->getRequest()->get("run")) {
                $this->_run = $this->getRequest()->get("run");
            }
        } else {
            $routes = $this->_routes;

            $url = implode("/", $urlArr);

            foreach ($routes as $sef => $param) {
                $sef = trim($sef, "/");

                if (preg_match("|^" . $sef . "$|i", $url)) {
                    $param = $param;
                    $sef = $sef;
                    break;
                }
            }

            if ($sef !== $url)
                return false;

            $route = explode('/', $param);

            $this->_controller = (isset($route[0]) && !empty($route[0])) ? $route[0] : null;

            $this->_view = (isset($route[1]) && !empty($route[1])) ? $route[1] : null;

            $this->_layout = (isset($route[2]) && !empty($route[2])) ? $route[2] : null;

            if ($this->getRequest()->get("run")) {
                $this->_run = $this->getRequest()->get("run");
            }
        }
    }

}

?>