<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Layout
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */
namespace maze\menu;
use maze\base\Object;

class Layout extends Object{
   
    /**
     * @var string -  название компанента 
     */
    protected $appName;
    /**
     * @var string -  название вида 
     */
    protected $view;
    
    /**
     * @var string - название шаблона
     */
    protected $layout;
         
    /**
     * @var string - заголовок шаблона 
     */
    protected $title;
    
    /**
     * @var string - описание шаблона 
     */
    protected $description;
    
    /**
     * @var string -  название контроллера 
     */
    protected $controller;
    
    /**
     * @var object -  параметры шаблона вида 
     */
    protected $params;
        
    public function getTitle()
    {
        return \Text::_($this->title);
    }
    
    public function getDescription()
    {
        return \Text::_($this->description);
    }
    
    public function getController()
    {
        return $this->controller;
    }
    
    public function getLayout()
    {
        return $this->layout;
    }
   
    public function getView()
    {
        return $this->view;
    }
    
    public function getParams()
    {
        if ($this->params !== null) return $this->params;

        $file_name = "params." . $this->layout . ".xml";

        $path = PATH_ROOT . DS . 'expansion' . DS . "exp_" . $this->appName . DS . 'views' . DS .$this->view . DS . "meta" . DS . $file_name;

        if (!file_exists($path))
            return false;

        $this->params = new \XMLConfig($path);

        return $this->params;
    }
}
