<?php

namespace ui\tmp;

use ui\select\Chosen;

class Mail extends Chosen {

    public $condition = [];

    public function init() {
        parent::init();
       
        if (!isset($this->condition['type']) || !isset($this->condition['name']) || !isset($this->condition['group'])) {
            throw new \Exception('Ошибка параметров');
        }
        $path = PATH_ROOT . DS;
        $front = isset($this->condition['front']) ? $this->condition['front'] : (defined("SITE") ? 1 : 0);
        switch ($this->condition['type']) {
            case "expansion":
                if (!$front) {
                    $path .= "admin" . DS;
                }
                $path .= "expansion" . DS . "exp_" . $this->condition['name'] . DS . "tpl" . DS;
                break;

            case "widgets":
                if (!$front) {
                    $path .= "admin" . DS;
                }
                $path .= "widgets" . DS . "wid_" . $this->condition['name'] . DS . "tpl" . DS;
                break;

            case "plugins":
                $path .= "plugins" . DS . $this->condition['name'] . DS . $options["name"] . DS . "tpl" . DS;
                break;

            case "gadgets":
                $path .= "admin" . DS . "gad_" . $this->condition['name'] . DS . "tpl" . DS;
                break;

            case "templates":
                if (!$front) {
                    $path .= "admin" . DS;
                }
                $path .= "templates" . DS . $this->condition['name'] . DS . "tpl" . DS;
                break;

            default:
                return false;
                break;
        }


        $files = scandir($path);
         
        $option = [];
        foreach ($files as $file) {
            if ($file == "." || $file == "..")
                continue;

            if (preg_match("#^tpl\.mail\.([a-z0-9-_\.^\.xml]+)\.xml$#i", $file, $matches)) {
             
                $tpl = \RC::getTplMail(array("type" => $this->condition['type'], "name" =>$this->condition['name'], "tpl" => $matches[1], "front" => $front));
                if ($tpl->group == $this->condition['group']) {
                    $option[$matches[1]] = $tpl->title;
                }
            }
        }



        $this->items = $option;
    }

}
