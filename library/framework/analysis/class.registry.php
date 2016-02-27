<?php

defined('_CHECK_') or die("Access denied");

class Registry {

    protected $reflection = null;
    protected $class = null;

    public function __construct($class, $args = null) {
        if (is_object($class)) {
            $this->reflection = new ReflectionObject($class);
            $this->class = $class;
        } elseif (is_string($class) && class_exists($class)) {
            $this->reflection = new ReflectionClass($class);

            if ($args == null) {
                $this->class = new $class();
            } elseif (is_array($args) && $this->reflection->getConstructor()->getNumberOfRequiredParameters() == count($arguments)) {
                $this->class = $this->reflection->newInstanceArgs($args);
            }
        }
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function get($name, $default = null) {
        if ($this->reflection == null)
            return false;

        if ($this->reflection->hasProperty($name)) {
            $refProp = $this->reflection->getProperty($name);
            return $refProp->getValue($this->class);
        } elseif ($default !== null) {
            return $default;
        }

        return false;
    }

    public function getAllProp() {
        if ($this->reflection == null)
            return false;
        $result = array();
        $properties = $this->reflection->getProperties();
        foreach ($properties as $prop) {
            if (($val = $this->get($prop->name))) {
                $result[$prop->name] = $val;
            }
        }
        return $result;
    }

    public function getObject() {
        return $this->class;
    }

}

?>