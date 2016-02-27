<?php

defined('_CHECK_') or die("Access denied");

use maze\base\Object;
use maze\languages\Languages;
use maze\exception\HttpException;

class Application extends Object {

    protected $_response;
    
    protected $_router;
    /**
     * @var Document - документ 
     */
    protected $_document;
    /**
     * @var Access - управление доступом 
     */
    protected $_access;
    /**
     * @var Languages - язык 
     */
    protected $_lang;
    /**
     * @var array - копилка объектов компанентов [name:maze\application\Component] 
     */
    protected $_component = [];
    /**
     * @var int - принадлежность сайта 
     */
    protected $_front;
    /**
     * @var maze\document\View экземпляр с класса предствления
     */
    protected $_view;
    /**
     * @var int ID стиля шаблона 
     */
    protected $_styleId;

    /**
     * @var Request - объект класса запроса
     */
    protected $_request;

    /**
     * @var object - конфигурация системы
     */
    protected $_config;

    /**
     * @var FrontController - экземпляр класса фронт контроллера компаненты
     */
    protected $_controller;

    /**
     * @var ToolBarAdmin|ToolBarSite - экземпляр панели инструментов
     */
    protected $_toolbar;

    /**
     * @var maze\conf\AppMenu - экземпляр системнго меню
     */
    protected $_menu;

    /**
     * @var Session - экземпляр класса сессии 
     */
    protected $_session;

    /**
     * @var object - тема оформления 
     */
    protected $_theme;
    
     /**
     *
     * @var array - навизационные цепочки
     */
    protected $_breadcrumbs = [];

    public function init() {
        $this->_front = defined("SITE") ? 1 : 0;
        RC::setAlias('@web', $this->getRequest()->getBaseUrl());
    }

    public function setBreadcrumbs($item) {
        $this->_breadcrumbs[] = $item;
    }
    
    public function setBreadcrumbsArr($items) {
        foreach($items as $path){
            $this->setBreadcrumbs($path);
        }
    }

    public function getBreadcrumbs() {
        return $this->_breadcrumbs;
    }
    
    public function getAccess() {

        if ($this->_access == null) {
            $this->_access = Access::instance();
        }
        return $this->_access;
    }

    public function getRequest() {
        if ($this->_request == null) {
            $this->_request = RC::createObject([
                    'class'=>'Request',
                    'enableCsrfValidation'=>(bool)$this->getConfig()->enableCsrfValidation
                ]);
        }
        return $this->_request;
    }

    public function getLang() {

        if ($this->_lang == null) {
            $this->_lang = Languages::instance();
        }
        return $this->_lang;
    }

    public function getDocument() {
        if ($this->_document == null) {
            $this->_document = Document::instance();
        }
        return $this->_document;
    }

    public function getConfig() {
        if ($this->_config == null) {
            $this->_config = RC::getConfig();
        }
        return $this->_config;
    }

    public function getMenu() {
        if ($this->_menu == null) {
            $this->_menu = RC::createObject(['class' => 'maze\conf\AppMenu']);
        }
        return $this->_menu;
    }

    public function getComponent($name) {
        if (!isset($this->_component[$name])) {
            $this->_component[$name] = RC::createObject(['class' => 'maze\application\Component', 'name' => $name]);
        }

        return $this->_component[$name];
    }
    
    public function getView(){
        if($this->_view == null){
           $this->_view = RC::createObject(['class' => 'maze\document\View']);
        }
        
        return $this->_view;
    }


    public function getController() {

        if ($this->_controller == null) {
            $this->_controller = FrontController::instance();
        }

        return $this->_controller;
    }

    public function getRouter() {
        if ($this->_router === null) {

            $plugin = RC::getPlugin("router");

            $plugin->triggerHandler("beforeloadRouter");
          
            $this->_router = RC::getRouter();

            $plugin->triggerHandler("afterloadRouter"); 
           
        }
        return $this->_router;
    }

    public function getResponse() {
        if ($this->_response === null) {
            $clear = $this->getRequest()->get("clear");
            $this->_response = RC::createObject([
                'class'=>'maze\document\Response',
                'charset'=>RC::app()->config->charset
            ]);
            
            if($this->getRequest()->isAjax() && $clear == 'ajax' && isset($this->_response->formatters[$clear])){                
                $this->_response->format = $clear;
            }
            elseif($clear && $clear != 'ajax' && isset($this->_response->formatters[$clear])){
                $this->_response->format = $clear;
            }
        }

        return $this->_response;
    }

    public function setResponse($format) {
       $this->getResponse()->format = $format;
    }
    
    public function getText($text) {
        return $this->lang->getText($text);
    }

    public function getSession() {
        if ($this->_session == null) {
            $this->_session = RC::createObject([
                'class'=>'Session', 
                'sesPath'=>$this->_config->get("ses_path"),
                'sesTime'=>intval($this->_config->get("ses_time")) * 60 ,
                'sesName'=>$this->_config->get("ses_name"),
                'sesSsl'=>false
            ]);
            
            
        }
        return $this->_session;
    }
    
    
    public function getExpUrl($url){
        if ($this->getRouter()->component !== null) {
            return $this->getRouter()->exp->getUrl($url);
        }
        return $url;
    }
    
    public function getExpPath($path){
        if ($this->getRouter()->component !== null) {
            return $this->getRouter()->exp->getPath($path);
        }
        return $path;
    }
   
    /**
     * Загрузка  компонента расширения
     * возвращает буферизированный контент
     * @param string $exp - название приложения
     */
    public function loadExp() {

        $exp = $this->getRouter()->component;
    
        if ($this->getComponent($exp)->is) {

            $path = $this->getComponent($exp)->path;
   
            return $this->getComponent($exp)->run();
        } else {
             throw new maze\exception\NotFoundHttpException(Text::_("LIB_FRAMEWORK_APPLICATION_NOT_EXP_FILE"));
        }
    }

    
    protected function loadUser() {
      
        $this->getAccess()->clearSessions();

        $this->getAccess()->openSession();

        $user = $this->getAccess()->get();

        if ($user && !empty($user['timezone'])) {
            date_default_timezone_set($user['timezone']);
        }
    }

    protected function loadSession() {
        if($this->getSession()){
            
            $this->getSession()->sessoinStart();
           
        }
       
    }
 
    
    public function __toString() {

        ini_set('default_charset', $this->_config->get("charset"));


        if ($this->isGzhandler()) {
            ob_start("ob_gzhandler");
        } else {
            ob_start();
        }

        ob_start("ob_iconv_handler");
       
        $this->getResponse()->send();  
        
        $route = RC::app()->router;
        
        $params = ['route'=>$route->getUrlPath(),
            'category'=>$route->getComponent(),
            'controller'=>$route->getController(),
            'action'=>$route->getRun(),
            'statusCode'=>$this->getResponse()->getStatusCode(),
            'statusText'=>$this->getResponse()->statusText,
            'requestHeaders'=>RC::app()->request->getHeaders()->toArray(),
            'responseHeaders'=>headers_list(),
            'get'=>$_GET,
            'post'=>$_POST,
            'cookie'=>$_COOKIE,
            'session'=>$_SESSION,
            'server'=>$_SERVER
        ];
       
        RC::getLog()->add('request',$params);
        
        $result = ob_get_clean();
        
        if(mb_detect_encoding($result) != $this->_config->get("charset")){
            $result = mb_convert_encoding($result, $this->_config->get("charset"));           
        }
        
        RC::getPlugin("system")->triggerHandler("afterRenderApplication", [&$result]);
        
        return $result;
    }

    protected function isGzhandler() {
        $str_enc = $this->getRequest()->getHeaders()->get("accept-encoding");
        if (substr_count($str_enc, "gzip") > 0 && $this->_config->get("gzip") && function_exists("ob_gzhandler") && extension_loaded('zlib')) {
            return true;
        } else {
            return false;
        }
    }

}

?>