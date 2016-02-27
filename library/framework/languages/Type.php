<?php

namespace maze\languages;

use maze\base\Object;
use maze\exception\Exception;

abstract class Type extends Object {

    public $front;
    public $langCode;
    public $name;
    public $type;
    protected $lang;
    protected $app;
    protected $constant;
    protected $overLoad;
    protected $langAll;
    protected $langDefaults;
    
    public function init() {
        $lang = $this->getLang();

        if (!$lang)
            return null;

        $lang_code = null;
        foreach ($lang as $l) {
            if ($l->defaults == 1) {
                $this->langDefaults = $l->lang;
            }
        }

        if ($this->langCode) {
            foreach ($lang as $l) {
                if ($l->lang->lang_code == $this->langCode) {
                    $lang_code = $l->lang->lang_code;
                    $this->lang = $l->lang;
                    $this->app = $l->app;
                    break;
                }
            }
        }

        if (!$lang_code) {
            foreach ($lang as $l) {
                if ($l->defaults == 1) {
                    $lang_code = $l->lang->lang_code;
                    $this->lang = $l->lang;
                    $this->app = $l->app;
                    break;
                }
            }
        }

        $this->langCode = $lang_code == null ? '' : $lang_code;
        $path = $this->getPath();
        if (file_exists($path)) {
            $this->constant = parse_ini_file($path);
        }


        $pathOverload = PATH_ROOT . DS . "language" . DS . "overload" . DS . $this->langCode . ".overload." . ($this->front ? "admin" : "site") . ".ini";

        if (file_exists($pathOverload)) {
            $this->overLoad = parse_ini_file($pathOverload);
        }
    }

    public function getIdLang() {
        if ($this->lang) {
            return $this->lang->id_lang;
        }
        return null;
    }

    public function getLangCode() {
        if ($this->lang) {
            return $this->lang->lang_code;
        }
        return null;
    }

    public function getReduce() {
        if ($this->lang) {
            return $this->lang->reduce;
        }
        return null;
    }

    public function getPath() {
        throw new Exception('Метод getPath обязателен для реализации ' . get_class($this));
    }

    public function getLang() {
        throw new Exception('Метод getLang обязателен для реализации ' . get_class($this));
    }
    
    public function getLangDefaults() {
         return $this->langDefaults;
    }

    public function getText($constant) {
        $constant = trim($constant);
        if ($this->overLoad && array_key_exists($constant, $this->overLoad)) {
            $constant = $this->overLoad[$constant];
        } elseif ($this->constant && array_key_exists($constant, $this->constant)) {
            $constant = $this->constant[$constant];
        }
        return $constant;
    }

}
