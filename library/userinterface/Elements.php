<?php
namespace ui;

use maze\base\Object;

defined('_CHECK_') or die("Access denied");

abstract class Elements extends Object{

    /**
     * @var int $counter - счетчик объектов Elements
    */
    public static $counter = 0;    
    /**
     * @var array $stack - копилка классов Elements
    */
    public static $stack = [];
    
    /**
     * @var string $IdPrefix - префикс для id
    */
    public static $IdPrefix = "elem";
    
    /**
     * @var array $scriptReg - зарегистрированные скриты
    */
    public static $scriptReg = [];
    /**
     * @var string $_id - id эелемента
    */
    protected $_id;
    
    public static function begin($config = []) {
        $config['class'] = get_called_class();

        $widget = \RC::createObject($config);
        static::$stack[] = $widget;
        return $widget;
    }
    
    public static function end() {
        if (!empty(static::$stack)) {
            $widget = array_pop(static::$stack);
            if (get_class($widget) === get_called_class()) {
                echo $widget->run();
                if(!self::isRegscript())
                {
                    static::$scriptReg[] = get_called_class();
                    $widget->registerClientScript();
                }
                
                return $widget;
            } else {
                throw new \Exception("Expecting end() of " . get_class($widget) . ", found " . get_called_class());
            }
        } else {
            throw new \Exception("Unexpected " . get_called_class() . '::end() call. A matching begin() is not found.');
        }
    }

    public static function element($config = []) {
        ob_start();
        ob_implicit_flush(false);
        $config['class'] = get_called_class();         
       
        $widget = \RC::createObject($config);
        
        if(!self::isRegscript())
        {
            static::$scriptReg[] = $config['class'];
            $widget->registerClientScript();
        }   
        $out = $widget->run();
        return ob_get_clean() . $out;
    }

    public function getId($autoGenerate = true) {
        if ($autoGenerate && $this->_id === null) {
            $this->_id = static::$IdPrefix . static::$counter++;
        }
        return $this->_id;
    }
    public static function isRegscript()
    {
        $class = get_called_class();
        return in_array($class, static::$scriptReg);
    }
    public function setId($value) {
        $this->_id = $value;
    }
    public function run()
    {
        
    }
    public function registerClientScript() {
        
    }

}
