<?php

defined('_CHECK_') or die("Access denied");

class ClassAutoloader {

    private $_arrDir;
    private $_prefix;
    private $_suffix;
    private static $classMap = [];

    public function __construct($path, $options = array("prefix" => "class", "suffix" => "php")) {
        $this->_arrDir = array();
        $this->_prefix = isset($options["prefix"]) ? $options["prefix"] . "." : "";
        $this->_suffix = isset($options["suffix"]) ? "." . $options["suffix"] : ".php";
        $this->scan_dir($path);

        array_push($this->_arrDir, get_include_path());

        $includePath = implode(PATH_SEPARATOR, $this->_arrDir);

        set_include_path($includePath);

        spl_autoload_register(array($this, 'loader'));
    }

    private function loader($className) {
        
        if(strpos($className, '\\') == false)
        {
            $file = $this->_prefix . strtolower($className) . $this->_suffix;       
            @include_once($file);
        }
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];

            if ($classFile[0] === '@') {
                $classFile = RC::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = RC::getAlias('@' . str_replace('\\', "/", $className) . '.php', false);

            if ($classFile === false || !is_file($classFile)) {

                return;
            }
        } else {
            return;
        }
        
        include($classFile);
    }

    private function scan_dir($directory) {
        $dir = opendir($directory);
        while (($file = readdir($dir)) !== false) {
            if (is_dir($directory . DIRECTORY_SEPARATOR . $file) && $file != "." && $file != "..") {
                array_push($this->_arrDir, $directory . DIRECTORY_SEPARATOR . $file);
                $this->scan_dir($directory . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

}

?>