<?php

defined('_CHECK_') or die("Access denied");

use maze\exception\NotFoundHttpException;
use maze\helpers\ArrayHelper;

class InstallApp extends Application {

    public $lang = "ru-RU";

    public function getRequest() {
        if ($this->_request == null) {
            $this->_request = RC::createObject([
                        'class' => 'Request',
                        'enableCsrfValidation' => true
            ]);
        }
        return $this->_request;
    }

    public function getController() {

        if ($this->_controller == null) {
            $this->_controller = RC::createObject([
                        'class' => 'maze\expansion\FrontInstallController'
            ]);
        }

        return $this->_controller;
    }

    public function getResponse() {
        if ($this->_response === null) {
            $clear = $this->getRequest()->get("clear");
            $this->_response = RC::createObject([
                        'class' => 'maze\document\Response'
            ]);


            if ($clear && isset($this->_response->formatters[$clear])) {
                $this->_response->format = $clear;
            }
        }

        return $this->_response;
    }

    public function getSession() {
        if ($this->_session == null) {
            $this->_session = RC::createObject([
                        'class' => 'Session',
                        'sesSsl' => false,
                        'sesName'=>'SIDINSTALL'
            ]);
        }
        return $this->_session;
    }

    public function getComponent($name) {
        if (!isset($this->_component[$name])) {
            $this->_component[$name] = RC::createObject(['class' => 'site\application\Component', 'name' => $name]);
        }

        return $this->_component[$name];
    }

    public function getRouter() {
        if ($this->_router === null) {
            $this->_router = RC::getRouter();
        }
        return $this->_router;
    }

    public function getView() {
        if ($this->_view == null) {
            $this->_view = RC::createObject(['class' => 'site\application\View']);
        }

        return $this->_view;
    }

    public function getText($text, $prop = []) {
        if ($this->lang && $this->lang !== null) {
            
            $parse = explode("_", mb_strtolower(trim($text)));
            if (!empty($parse)) {                
                $path = RC::getAlias("@root/language/framework/" . $this->lang . ".lib.".$parse[1].".$parse[2].ini");
                if (file_exists($path)) {
                    $langArr = parse_ini_file($path);
                    $text = array_key_exists($text, $langArr) ? $langArr[$text] : $text;
                }
            }
        }

        return $text;
    }

    public function __toString() {

        ini_set('default_charset', 'UTF-8');

        ob_start();
        ob_start("ob_iconv_handler");
        $this->getResponse()->send();
        return ob_get_clean();
    }

    public function getTheme() {

        if ($this->_theme === null) {
            $this->_theme = RC::createObject([
                        'class' => 'maze\base\Theme',
                        'name' => 'defaultinstall',
                        'basePath' => '@tmp/defaultinstall',
                        'param' => [],
                        'front' => 1,
                        'baseUrl' => '@web/install/templates/defaultinstall'
            ]);
        }

        return $this->_theme;
    }

    public function dispatcher() {

        $this->loadSession();
        $this->getRouter()->dispatcher();
        
        $this->document->set("type", "text/html");
        $this->document->set("charset", 'utf-8');
        $this->document->set("title", RC::app()->getText("LIB_FRAMEWORK_INSTALL_TITLE"));

        $this->document->set("favicon", $this->getTheme()->getUrl('favicon.png'));

        $this->getView()->component = $this->loadExp();


        $this->document->setBobyClass(['component-' . $this->getRouter()->exp->name, 'controller-' . $this->getRouter()->controller]);
        if ($this->getRouter()->view) {
            $this->document->setBobyClass('view-' . $this->getRouter()->view);
        }
        if ($this->getRouter()->layout) {
            $this->document->setBobyClass('view-layout-' . $this->getRouter()->layout);
        }
        $this->document->setHtmlClass('theme-' . $this->getTheme()->name);
        $this->document->setHtmlClass(['page-install', $this->request->gerBrowser(), $this->request->getOS()]);

        return $this;
    }

}

?>