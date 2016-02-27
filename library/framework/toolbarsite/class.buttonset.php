<?php

defined('_CHECK_') or die("Access denied");

class Buttonset extends AbstractElement {
    
    const BTNBIG =  'BIG';
    
    const BTNMIN =  'MIN';
   /**
    *
    * @var string - ID кнопки определяет уникальность кнопки
    */
    protected $id;
    /**
     *
     * @var string - название кнопки 
     */
    protected $title; 
    /**
     *
     * @var string - размеры кнопки MIN или BIG
     */
    protected $type = "MIN";
    /**
     *
     * @var int - сортировка внутри группы
     */
    protected $sort;  
    /**
     *
     * @var int - сортировка группы
     */
    protected $sortgroup; 
    /**
     *
     * @var string -  URL для перехода или javascript:MyJSFunction())
     */
    protected $href;
    /**
     *
     * @var string - название CSS-класса с иконкой кнопки
     */
    protected $icon; 
   /**
    *
    * @var string - путь к иконке кнопки 
    */
    protected $src; 
    /**
     *
     * @var string - Javascript-код
     */
    protected $action; 
    /**
     *
     * @var string - подсказка кнопки
     */
    protected $hint;
    /**
     *
     * @var bool - видимость кнопки
     */
    protected $visible = true;


    public function __construct(array $button) {
        parent::__construct();

        if (isset($button["ID"])) {
            $this->id = $button["ID"];
        } else {
            $this->id = 'button-set-sitebat-' . static::$countelem;
        }
        if (isset($button["TITLE"])) {
            $this->title = $button["TITLE"];
        }
        if (isset($button["TYPE"])) {
            $this->type = $button["TYPE"];
        }
        if (isset($button["SORT"])) {
            $this->sort = $button["SORT"];
        }
        if (isset($button["SORTGROUP"])) {
            $this->sortgroup = $button["SORTGROUP"];
        }
        if (isset($button["HREF"])) {
            $this->href = $button["HREF"];
        }
        if (isset($button["ICON"])) {
            $this->icon = $button["ICON"];
        }
        if (isset($button["SRC"])) {
            $this->src = $button["SRC"];
        }
        if (isset($button["ACTION"])) {
            $this->action = $button["ACTION"];
        }
        if (isset($button["HINT"]) && is_array($button["HINT"]) && isset($button["HINT"]["TITLE"]) && isset($button["HINT"]["TEXT"])) {
            $this->hint = $button["HINT"];
        }
        if (isset($button["MENU"]) && is_array($button["MENU"])) {
            $this->addMenuArray($button["MENU"]);
        }
        if (isset($button["VISIBLE"])) {
            $this->visible = $button["VISIBLE"];
        }
    }
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getSort()
    {
        return $this->sort;
    }
    
    public function getSortgroup()
    {
        return $this->sortgroup;
    }
    
    public function getHref()
    {
        return $this->href;
    }
    
    public function getIcon()
    {
        return $this->icon;
    }
    
    public function getSrc()
    {
        return $this->src;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    public function getHint()
    {
        return $this->hint;
    }
    
    public function getVisible()
    {
        return $this->visible;
    }

}

?>