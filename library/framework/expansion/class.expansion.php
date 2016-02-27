<?php

defined('_CHECK_') or die("Access denied");

/**
 *  АБСТРАКТНЫЙ БАЗОВЫЙ КЛАСС КОТРОЛЛЕРА
 */
abstract class Expansion {

    protected $_config;
    public $_rout;
    public $_cache;
    public $_lang;
    public $_ses;
    public $_doc;
    public $document;
    public $_access;
    public $_uri;
    public $router;
    protected $front;
    protected $_appname;
    public $request;

    public function __construct() {
        if (defined("SITE"))
            $this->front = true;
        if (defined("ADMIN"))
            $this->front = false;
        $this->_rout = RC::app()->router;
        $this->router = RC::app()->router;
        $this->_access = RC::app()->access;
        $this->_config = RC::app()->config;
        $this->_cache = RC::getCache("exp_" . $this->_rout->component);
        $this->_lang = RC::app()->lang->getLang(['type'=>'exp', 'name'=>$this->_rout->component]);
        $this->_ses = RC::app()->session;
        $this->_doc = RC::app()->document;
        $this->document = RC::app()->document;
        $this->_appname = $this->_rout->component;
        $this->_uri = URI::instance();
        
        $this->_cache->setTimeLive($this->_rout->exp->time_cache);
        $this->_cache->setEnable($this->_rout->exp->enable_cache);
        $this->request = RC::app()->getRequest();
        $this->init();
    }
    public function init(){}
    
    public function __get($index) {
        if (isset($this->$index)) {
            return $this->$index;
        }
    }

    final public function setRedirect($url = '') {
        $this->_doc->setRedirect($url);
    }

    final public function setMessage($text, $type) {
        $this->_doc->setMessage($text, $type);
    }


    final protected function model($model, array $args = []) {

        $pach = 'exp\exp_' . $this->_rout->component . '\model\\' . $model;

        if (class_exists($pach)) {
            $refClass = new ReflectionClass($pach);

            if ($refClass->isSubclassOf('maze\base\Model')) {

                return RC::createObject(array_merge($args, ['class' => $pach]));
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_NOT_PARENT_CLASS") . $pach);
            }
        } else {
           throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_NOT_CLASS") . $pach);
        }
    }
    
    final protected function form($model, array $args = []) {

        $pach = 'exp\exp_' . $this->_rout->component . '\form\\' . $model;
        if (class_exists($pach)) {
            $refClass = new ReflectionClass($pach);

            if ($refClass->isSubclassOf('maze\base\Model')) {

                return RC::createObject(array_merge($args, ['class' => $pach]));
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_NOT_PARENT_CLASS") . $pach);
            }
        } else {
            throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_NOT_CLASS") . $pach);
        }
    }

   
    final protected function table($model, array $args = []) {

        $pach = 'exp\exp_' . $this->_rout->component . '\table\\' . $model;

        if (class_exists($pach)) {
            $refClass = new ReflectionClass($pach);

            if ($refClass->isSubclassOf('\maze\db\ActiveRecord')) {

                return RC::createObject(array_merge($args, ['class' => $pach]));
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_TABLE_NOT_PARENT_CLASS") . $pach);
            }
        } else {
            throw new Exception(Text::_("LIB_FRAMEWORK_EXPANSION_TABLE_NOT_CLASS") . $pach);
        }
    }

    public function packData($data) {
        return base64_encode(serialize($data));
    }

    public function unPackData($data) {
        return unserialize(base64_decode($data));
    }

    public function PackDataJSON($data) {
        return base64_decode(json_decode($data));
    }

    public function unPackDataJSON($data) {
        return json_decode(base64_decode($data));
    }

    public function getParams($name) {
        $conf = $this->_rout->exp->config;
        return $conf->getVar($name);
    }

    public function getConfApp() {
        return $this->_rout->exp->config;
    }

    public function getAllParams() {
        $conf = $this->_rout->exp->config;
        return $conf->getAllValue();
    }

}

?>