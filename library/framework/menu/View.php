<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MenuView
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */

namespace maze\menu;

use maze\base\Object;

class View extends Object {

    /**
     * @var string -  название вида 
     */
    protected $view;

    /**
     * @var string - название компаненты 
     */
    protected $appName;

    /**
     * @var string - название вида 
     */
    protected $title;

    /**
     * @var string - описание вида 
     */
    protected $description;

    /**
     * @var array|object -  массив объектов параметров вида 
     */
    protected $layouts;

    /**
     * @var array - массив конфига 
     */
    public $layoutset;

    public function getTitle() {
        return \Text::_($this->title);
    }

    public function getDescription() {
        return \Text::_($this->description);
    }

    public function getView() {
        return $this->view;
    }

    public function getLayouts() {
        if ($this->layouts !== null)
            return $this->layouts;

        $this->layouts = [];
        
        
       
        if ($this->layoutset) {
            foreach ($this->layoutset as $layout) {

                if(empty($layout)) continue;
                $this->layouts[$layout['layout']] = \RC::createObject([
                            'class' => 'maze\menu\Layout',
                            'view' => $this->view,
                            'appName' => $this->appName,
                            'title' => $layout['title'],
                            'description' => $layout["description"],
                            'layout' => $layout['layout'],
                            'controller' => $layout['controller']
                ]);
            }
        }
       

        return $this->layouts;
    }

    public function getLayout($name) {
        $this->getLayouts();
        return isset($this->layouts[$name]) ? $this->layouts[$name] : null;
    }

}
