<?php

namespace maze\cache;

use maze\helpers\StringHelper;

abstract class Cache extends \maze\base\Object{
    
    /**
     * @var string type - группа кеша
     */
    public $type;

    /**
     * @var int time - время жизни кеша
     */
    public $time = 1200;
    
    /**
     * @var boolean enable - включить кеширование
     */
    public $enable = 1;
    
    
    /**
     * @var  function serializer - обработчик сериализации
     */
    public $serializer;
    
    /**
     * @var  function unserializer - обработчик де сериализации
     */
    public $unserializer;
    
   
     
    /**
     * Получить ключ кеша
     * 
     * @param mixed $key - ключь кеша
     * @return string
     */
    public function buildKey($key)
    {
         
        if (is_string($key)) {
            $key = ctype_alnum($key) && StringHelper::byteLength($key) <= 32 ? $key : md5($key);
        } else {
            $key = md5(json_encode($key));
        }

        return  $key;
    }
    
    /**
     * Получить данные кеша
     * 
     * @param mixed $key - ключь кеша
     * @return boolean|\maze\cache\Dependency
     */
    public function get($key)
    {
         
        if(!$this->enable) return false;
        
        $key = $this->buildKey($key);
        $value = $this->getValue($key);
    
        if ($value === false || $this->serializer === false) {
            return $value;
        } elseif ($this->unserializer === null) {
            $value = unserialize($value);
        } else {
            $value = call_user_func($this->unserializer, $value);
        }
        
        return $value;
    }
    /**
     * Установить данные кеша
     * 
     * @param mixed $key - ключь кеша
     * @return boolean|\maze\cache\Dependency
     */
    public function set($key, $value)
    {
        if(!$this->enable) return false;
        
        if ($this->serializer === null) {
            $value = serialize($value);
        } elseif ($this->serializer !== false) {
            $value = call_user_func($this->serializer, $value);
        }
        $key = $this->buildKey($key);

        return $this->setValue($key, $value);
    }
    
    public function clearTypeFull()
    {
        return $this->deleteType(); 
    }
    
    public function clear($key){
        $key = $this->buildKey($key);
        return $this->deleteValue($key);
    }


    public function setTimeLive($val) {
        if($val === null) return;
        $this->time = $val;
    }

    public function setEnable($val) {
        $this->enable = $val;
    }
    
     public function exists($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);

        return $value !== false;
    }
    
    /**
     * Поучить кеш данные
     * 
     * @param string $key - ключь кеша
     */
    abstract protected function getValue($key);
    
    /**
     * Установить кеш данные
     * 
     * @param string $key - ключь кеша
     * @param string $value - данные кеша 
     */
    abstract protected function setValue($key, $value);
    
    /**
     * Удалить кеш данные
     * 
     * @param string $key - ключь кеша
     */
    abstract protected function deleteValue($key);
    
    /**
     * Удалить всего тип кеш данные
     * 
     * @param string $key - ключь кеша
     */
    abstract protected function deleteType();
}