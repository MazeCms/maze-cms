<?php

namespace exp\exp_dictionary;

class Install extends \maze\install\ExpInstall {

    public $name = "dictionary";
    
    public $front = [1, 0];
    
    public $enabled = 1;
    
    public $group = "content";
    
    public $lang = ["admin" => ["ru-RU"], "site" => ["ru-RU"]];
    
    public $defaultLang = ["admin" => "ru-RU", "site" => "ru-RU"];

    public function getCommands() {
        return [
            'init' => $this->text("EXP_DICTIONARY_CHECK"),
            'copy' => $this->text("EXP_DICTIONARY_SCRIPTS"),
            'add' => $this->text("EXP_DICTIONARY_INSTALLBD"),
            'remove' => $this->text("EXP_DICTIONARY_REMOVETEMP")
        ];
    }

}

?>