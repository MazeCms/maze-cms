<?php

namespace exp\exp_sitemap;

class Install extends \maze\install\ExpInstall {

    public $name = "sitemap";
    
    public $front = [0, 1];
    
    public $enabled = 1;
    
    public $group = "content";
    
    public $lang = ["admin" => ["ru-RU"], "site"=>["ru-RU"]];
    
    public $sql = ['install.sql'];

    public $defaultLang = ["admin" => "ru-RU", "site"=>"ru-RU"];


    public function getCommands() {
        return [
            'init' => $this->text("EXP_SITEMAP_CHECK"),
            'sql'=>$this->text("EXP_SITEMAP_INSTALLTABLE"),
            'copy' => $this->text("EXP_SITEMAP_SCRIPTS"),
            'add' => $this->text("EXP_SITEMAP_INSTALLBD"),
            'remove' => $this->text("EXP_SITEMAP_REMOVETEMP")
        ];
    }

}

?>