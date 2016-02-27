<?php

defined('_CHECK_') or die("Access denied");

use maze\exception\UserException;

class LoaderPlugin extends Event {

    private static $_plugin = array();
    private static $_instance;

    private function __clone() {
        
    }

    public function __construct() {
        
    }

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function loadPlugin($type) {

        try {
            $activ = $this->getPlugin($type);

            $this->type_event = $type;

            foreach ($activ as $plg) {
                try {

                    $this->createPlugin($plg);
                    
                } catch (ExceptionCore $exp) {
                    RC::getLog()->add('error', [
                        'file' => $exp->getFile(),
                        'line' => $exp->getLine(),
                        'code' => $exp->getCode(),
                        'message' => $exp->getMessage(),
                        'category' => get_class($exp)]);
                }
            }
        } catch (Exception $ex) {
            RC::getLog()->add('error', [
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'code' => $ex->getCode(),
                'message' => $ex->getMessage(),
                'category' => get_class($ex)]);
        }
    }

    public function triggerHandler($metod, $arguments = false) {
        try {
            parent::triggerHandlerList($this->type_event, $metod, $arguments);
        } catch (Exception $exp) {
            RC::getLog()->add('error', [
                'file' => $exp->getFile(),
                'line' => $exp->getLine(),
                'code' => $exp->getCode(),
                'message' => $exp->getMessage(),
                'category' => get_class($exp)]);
        }
    }

    private function createPlugin($plugin) {
        static $paths = array();

        $path = PATH_PUGINS . DS . $this->type_event . DS . $plugin['name'] . DS . "plg." . $this->type_event . "." . $plugin['name'] . ".php";

        if (!file_exists($path)) {
            throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_LOADERPLUGIN_NOFILE", array($path)), 300);
            return false;
        }
        if (!isset($paths[$path])) {
            require_once $path;
            $paths[$path] = true;
        }

        $className = ucfirst(strtolower($plugin['name'])) . "_Plugin_" . ucfirst(strtolower($this->type_event));

        if (!class_exists($className)) {
            throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_LOADERPLUGIN_NOCLASS", array($className)), 300);
        }
        $refClass = new ReflectionClass($className);

        if (!$refClass->isSubclassOf('Plugin')) {
            throw new UserException(Text::_("LIB_FRAMEWORK_APPLICATION_LOADERPLUGIN_NOPARENT", array($className, "Plugin")), 300);
        }
        $obj = $refClass->newInstance($this->type_event, $plugin);
    }

    private function getPlugin($type) {
        $front = defined("SITE") ? 1 : 0;

        $key = md5($type . $front);

        if (!empty(self::$_plugin[$key])) {
            return self::$_plugin[$key];
        }

        $id_role = Access::instance()->getIdRole();

        $tbn = maze\table\Plugin::tableName();

        $result = maze\table\Plugin::find()
                        ->joinWith(['installApp', 'accessRole'], false)
                        ->andOnCondition([
                            'ia.front_back' => $front,
                            $tbn . '.group_name' => $type,
                            $tbn . '.enabled' => 1
                        ])
                        ->andOnCondition(['or', ['in', 'ar.id_role', (!empty($id_role) ? $id_role : 0)], 'ar.id_role is null'])
                        ->orderBy($tbn . '.ordering')
                        ->asArray()->all();

        return self::$_plugin[$key] = $result;
    }

}

?>