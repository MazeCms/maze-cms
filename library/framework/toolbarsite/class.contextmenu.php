<?php

defined('_CHECK_') or die("Access denied");

class ContextMenu extends AbstractElement {

    /**
     *
     * @var string - название пункта меню
     */
    protected $title; 
    /**
     *
     * @var int - сортировка внутри группы
     */
    protected $sort;  
    /**
     *
     * @var string - URL для перехода или javascript:MyJSFunction())
     */
    protected $href;
    /**
     *
     * @var string - путь к иконке с картинкой 
     */
    protected $src;
    /**
     *
     * @var bool  разделитель
     */
    protected $separator; 
    /**
     *
     * @var string  Javascript-код
     */
    protected $action;
    /**
     *
     * @var bool - видимость пункта меню
     */
    protected $visible = true;

    public function __construct(array $menu) {
        parent::__construct();

        if (isset($menu["TITLE"])) {
            $this->title = $menu["TITLE"];
        }

        if (isset($menu["SORT"])) {
            $this->sort = $menu["SORT"];
        }

        if (isset($menu["HREF"])) {
            $this->href = $menu["HREF"];
        }

        if (isset($menu["SRC"])) {
            $this->src = $menu["SRC"];
        }
        if (isset($menu["SEPARATOR"])) {
            $this->separator = $menu["SEPARATOR"];
        }

        if (isset($menu["ACTION"])) {
            $this->action = $menu["ACTION"];
        }
        if (isset($menu["MENU"])) {
            $this->addMenuArray($menu["MENU"]);
        }
        if (isset($menu["VISIBLE"])) {
            $this->visible = $menu["VISIBLE"];
        }
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getSort()
    {
        return $this->sort;
    }
    
    public function getHref()
    {
        return $this->href;
    }
    
    public function getSrc()
    {
        return $this->src;
    }
    
    public function getSeparator()
    {
        return $this->separator;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function getVisible()
    {
        return $this->visible;
    }

}

?>