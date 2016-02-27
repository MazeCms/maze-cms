<?php

defined('_CHECK_') or die("Access denied");

class Debugger {

    protected $_start = 0;
    protected $_prefix = '';
    protected $previousTime = 0.0;
    protected $previousMem = 0.0;
    protected $buffer = null;
    protected static $_instances = array();

    public function __construct($prefix = '') {
        $this->start = $this->getmicrotime();
        $this->prefix = $prefix;
        $this->buffer = array();
    }

    public static function instance($prefix = '') {
        if (empty(self::$_instances[$prefix])) {
            self::$_instances[$prefix] = new self($prefix);
        }

        return self::$_instances[$prefix];
    }

    public function mark($label) {
        $current = self::getmicrotime() - $this->_start;
        $currentMem = 0;

        $currentMem = memory_get_usage() / 1048576;
        $mark = sprintf(
                '<span>%s милисекунд %.3f секунд (+%.3f); размер скрипта %0.2f MB (%s%0.3f) загрузка компонентов - %s</span>', $this->prefix, $current, $current - $this->previousTime, $currentMem, ($currentMem > $this->previousMem) ? '+' : '', $currentMem - $this->previousMem, $label
        );

        $this->previousTime = $current;
        $this->previousMem = $currentMem;
        $this->buffer[] = $mark;

        return $mark;
    }

    public function getMemory() {
        return memory_get_usage();
    }

    public static function getmicrotime() {
        list ($usec, $sec) = explode(' ', microtime());

        return ((float) $usec + (float) $sec);
    }

    public function getBuffer() {
        return $this->buffer;
    }

}

?>