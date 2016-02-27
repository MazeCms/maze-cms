<?php

defined('_CHECK_') or die("Access denied");

class Parseparams {

    protected $params = null;

    const USERIALIZE = "unserialize";
    const DECOSEJSON = "json_decode";

    public function __construct($param, $const = Parseparams::USERIALIZE) {
        if (is_array($param)) {
            $this->params = $param;
        } elseif (is_string($param)) {

            $this->params = call_user_func($const, $param);
        }
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function get($name, $default = null) {
        $result = false;
        if (is_array($this->params)) {
            $result = isset($this->params[$name]) ? $this->params[$name] : false;
        } elseif (is_object($this->params)) {
            $result = isset($this->params->$name) ? $this->params->$name : false;
        }

        if (!$result && $default !== null) {
            $result = $default;
        }
        return $result;
    }

}

?>