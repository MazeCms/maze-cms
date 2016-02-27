<?php

defined('_CHECK_') or die("Access denied");

/**
 * АБСТРАКТНЫЙ КЛАСС ВИДА
 */
class View extends Expansion {

    /**
     * @var string Имя шаблона  @var String
     */
    protected $_layout;

    /**
     * @var boolean Переопределить шаблон или нет  
     */
    protected $_front;

    /**
     * @var array - Копилка для переменых вида  
     */
    protected $_vars = [];

    /**
     * @var string - буфер текущего объекта вида
     */
    protected $_render;
    
    
    

    /**
     * Конструктор класса
     * @param array $layout - массив шаблонов (по умолчанию и альтернативный)
     * @param  bool $front = true Переопределить шаблон или нет
     * @return View 
     */
    public function __construct($config = []) {
        parent::__construct();
        
        foreach ($config as $key=>$value) {
            
            if (!empty($key)) {             
              
                $setter = 'set' . ucfirst($key);
                
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                } else {
                    throw new Exception('Getting unknown property: ' . get_class($this) . '::' . $key);
                }
            }
        }

    }

    
    /**
     * АБСТРАКТНЫЙ МЕТОД инициализации вида
     */
    public function registry() {
        
    }

    /**
     * АКСЕССОР МЕТОД ДЛЯ НАЗНАЧЕНИЯ ПЕРЕМЕННЫХ ВИДА
     * 
     * @param string $key - имя переменной в виде
     * @param mixed $value - значение
     */
    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    /**
     * НАЗНАЧЕНИЕ ПЕРЕМЕННЫХ ВИДА
     * 
     * @param $var (mixed) 1) если string, тогда имя переменной в виде, 2) если array, то массив array( 'var' => 'value' )
     * @param $value (mixed)  - значение переменной вида для случая (1)
     */
    public function set($var, $value = '') {
        if (is_array($var)) { // array( 'var1' => 'value1', 'var2' => 'value2' )
            $keys = array_keys($var); // array( 'var1', 'var2' )
            $values = array_values($var); // array( 'value1', 'value2' )
            $this->_vars = array_merge($this->_vars, array_combine($keys, $values));
        } elseif (is_object($var)) {
            $var = get_object_vars($var);
            if (empty($var))
                return false;
            $keys = array_keys($var);
            $values = array_values($var);
            $this->_vars = array_merge($this->_vars, array_combine($keys, $values));
        }
        else {
            $this->_vars[$var] = $value;
        }
        return true;
    }

    public function get($var, $default = false) {
        if (isset($this->_vars[$var])) {
            return $this->_vars[$var];
        } elseif (!($default === false)) {
            return $default;
        }
        return false;
    }
    
    public function setVar(array $vars){
         return $this->set($vars);
    }

    /**
     * НАЗНАЧЕНИЕ LAYOUT-А
     * 
     * @param (string) $layout - путь к layout-у от
     */
    public function setLayout($value) {
        return $this->_layout = $value;
    }
    
    public function getLayout() {
       return $this->_layout;
    }
    
    /**
     * Вывод страницы в браузер
     */
    public function render() {
        
        if($this->_render === null){
           
            $this->_render = RC::app()->view->render('/'.$this->_layout, $this->_vars);  
        }        
       
        return  $this->_render;
    }

}
