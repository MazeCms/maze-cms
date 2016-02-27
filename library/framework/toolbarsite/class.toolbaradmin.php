<?php

defined('_CHECK_') or die("Access denied");
use maze\base\Object;

class ToolBarAdmin extends Object{

    /**
     *
     * @var array - массив кнопокн разделенных на группы вида
     */
    protected $group = [];
    
    public function addGroup($group, $button) {
        if(is_array($button))
        {
            $button = RC::createObject($button);
        }
        
        if(is_object($button))
        {
            $refClass = new \ReflectionClass($button);

            if (!$refClass->isSubclassOf('AbstractElement')) {
                throw new Exception("Объект кнопки должен наследовать класс AbstractElement");
            }
            
        }
        else
        {
            throw new Exception("Ошибка параметра вызова метода ToolBarsite::addGroup,button неявляется объектом ");
        }
        
        if (!isset($this->group[$group])) {
            $this->group[$group] = ["BTN" => [], "ORDERING" => count($this->group)];
        }

        if ($button->sortgroup !== null) {
            $this->group[$group]["ORDERING"] += $button->sortgroup;
        }

        if ($button->sort == null) {
            $button->sort = count($this->group[$group]["BTN"]);
        }

        $this->group[$group]["BTN"][] = $button;
    }

    public function addGroupArray($group, array $buttons) {
        foreach ($buttons as $btn) {
            $this->addGroup($group, $btn);
        }
    }

    /*
     * 	Сортировка групп (обратный порядок т.е. чем тяжелее сумма каждой группы кнопоко тем выше группа)
     */

    private function sortGroup($a, $b) {
        if ($a["ORDERING"] == $b["ORDERING"]) {
            return 0;
        }

        return ($a["ORDERING"] > $b["ORDERING"]) ? -1 : 1;
    }

    private function sortBTNGroup($a, $b) {
        if ($a->sort == $b->sort) {
            return 0;
        }

        return ($a->sort > $b->sort) ? -1 : 1;
    }

    public function getGroup() {

        usort($this->group, array($this, "sortGroup"));

        $result = array();

        foreach ($this->group as $name => $group) {
            usort($group["BTN"], array($this, "sortBTNGroup"));
            $result[$name] = $group["BTN"];
        }

        return $result;
    }


}

?>