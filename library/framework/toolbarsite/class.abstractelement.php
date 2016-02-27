<?php

defined('_CHECK_') or die("Access denied");

use maze\base\Object;

abstract class AbstractElement extends Object{

    protected $menu = array(); // меню
    
    protected static $countelem = 0;

    public function __construct() {
        static::$countelem++;
    }
    
    public function addMenu($menu) {
        if(is_array($menu))
        {
            $menu = RC::createObject($menu);
        }
        
        if(is_object($menu))
        {
            $refClass = new \ReflectionClass($menu);

            if (!$refClass->isSubclassOf('AbstractElement')) {
                throw new Exception("Объект меню должен наследовать класс AbstractElement");
            }
            
        }
        else
        {
            throw new Exception("Ошибка параметра вызова метода AbstractElement::addMenu, menu неявляется объектом ");
        }
        
        if ($menu->sort == null) {
            $menu->sort = count($this->menu);
        }
        $this->menu[] = $menu;
    }

    protected function sortMenu($a, $b) {
        if ($a->sort == $b->sort) {
            return 0;
        }

        return ($a->sort > $b->sort) ? -1 : 1;
    }

    public function addMenuArray(array $menu) {
        foreach ($menu as $item) {
            $this->addMenu($item);
        }
    }

    public function getMenu() {
        usort($this->menu, array($this, "sortMenu"));
        return $this->menu;
    }
    
    public function setMenu($menu) {
        $this->addMenu($menu);
    }

}

?>