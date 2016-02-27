<?php

defined('_CHECK_') or die("Access denied");

class CreateConfig {

    private static $_instance;
    private $params = array();
    private $buffer = array();
    

    private function __construct() {
        
    }

    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getData($params) {
        $this->params = $params;
    }

    protected function setBuffer($text, $sipirator = "") {
        $this->buffer .= $text . $sipirator;
    }

    protected function clearBuffer() {
        $this->buffer = "";
    }

    protected function getTextConfig() {
        if (empty($this->params))
            return false;
        $this->clearBuffer();
        $this->setBuffer("<?php", "\n");
        $this->setBuffer("class Config", "\n");
        $this->setBuffer("{", "\n\t");
        if (is_array($this->params)) {
            foreach ($this->params as $name => $value) {
                $this->setBuffer("public", " ");
                $this->setBuffer("$" . $name, " = ");
                $this->getTypeValue($value);
                $this->buffer = chop($this->buffer, ",\n\t;");
                $this->setBuffer(";", "\n\t");
            }
        }
        $this->setBuffer("}", "\n");
        $this->setBuffer("?>", "\n");
    }

    protected function getTypeValue($value) {
        if (is_array($value)) {
          
            $this->setBuffer("[", "\n\t\t");

            foreach ($value as $key => $val) {
                $this->setBuffer("'" . $key . "'=>", "");
                $this->getTypeValue($val);
            }
            $this->buffer = chop($this->buffer, ",");
            $this->setBuffer("],", "");
        } else {
            $this->setBuffer("'" . $value . "'", ",\n\t\t");
        }
    }

    public function saveFile($path, $filename) {
        $this->getTextConfig();
        $path = rtrim($path, "\\/");

        if (!is_dir($path))
            return false;

        file_put_contents($path . DS . $filename, $this->buffer);
        if (file_exists($path . DS . $filename))
            return true;
        return false;
    }

}

?>