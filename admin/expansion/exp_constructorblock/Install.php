<?php

namespace exp\exp_constructorblock;

class Install extends \maze\install\ExpInstall {

    public $name = "constructorblock";
    
    public $front = [0];
    
    public $enabled = 1;
    
    public $group = "content";
    
    public $lang = ["admin" => ["ru-RU"]];
    
    public $sql = ['install.sql'];

    public $defaultLang = ["admin" => "ru-RU"];


    public function getCommands() {
        return [
            'init' => $this->text("EXP_CONSTRUCTORBLOCK_CHECK"),
            'sql'=>$this->text("EXP_CONSTRUCTORBLOCK_INSTALLTABLE"),
            'copy' => $this->text("EXP_CONSTRUCTORBLOCK_SCRIPTS"),
            'add' => $this->text("EXP_CONSTRUCTORBLOCK_INSTALLBD"),
            'remove' => $this->text("EXP_CONSTRUCTORBLOCK_REMOVETEMP")
        ];
    }

}

?>