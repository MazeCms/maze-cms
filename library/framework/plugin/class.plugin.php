<?php

defined('_CHECK_') or die("Access denied");

abstract class Plugin extends Event {

    private $plugin;
    protected $plgname;
    protected $plgtype;
    protected $options; // парамерты из БД ввиде одномерно массива (array) ['title'], ['id_plg'], ['name'], ['param']	
    protected $params;
    protected $_doc;
    protected $_access;
    protected $_config;
    protected $_ses;

    public function __construct($type, $options) {

        $this->plgtype = $type;
        $this->plgname = $options["name"];
        $this->options = $options;
        $this->setObject($type, $this); // добавляем объект события плагина 
        $this->_access = Access::instance();
        $this->_config = RC::getConfig();
        $this->_ses = RC::app()->session;
        $this->_doc = Document::instance();
        $this->params = $this->getParamAll();
    }

    protected function getParamAll() {
        $params = $this->options['param'];
        return RC::getConf(array("type" => "plugin", "group" => $this->plgtype, "name" => $this->plgname), $params);
    }

    protected function getParam($name) {
        $params = $this->options['param'];
        $conf = RC::getConf(array("type" => "plugin", "group" => $this->plgtype, "name" => $this->plgname), $params);
        return $conf->getVar($name);
    }

    protected function __call($method, $arg) {
        //if(!is_array($arg)) return false;

        $class = get_class($this);

        $pattern = "|([-0-9a-zA-Z]+)[_]+([-0-9a-zA-Z]+)[_]+([-0-9a-zA-Z]+)|i";

        $name = false;

        if ($method == "getName") {
            $name = preg_replace($pattern, "\\1", $class);
        } elseif ($method == "getType") {
            $name = preg_replace($pattern, "\\3", $class);
        } elseif ($method == "getBase") {
            $name = preg_replace($pattern, "\\2", $class);
        }

        return strtolower($name);
    }

}

?>