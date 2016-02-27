<?php 
namespace exp\exp_sitemap;

class Uninstall extends \maze\install\ExpUninstall {

    public $name = "sitemap";

    public $front = [0,1];
    
    public $sql = ['uninstall.sql'];

    public function getCommands() {
        return [
            'init' => $this->text("EXP_SITEMAP_CHECK"), 
            'sql'=>$this->text("EXP_SITEMAP_UNINSTALLTABLE"), 
            'del'=>$this->text("EXP_SITEMAP_DELETEDB"),
            'remove' => $this->text("EXP_SITEMAP_DELETE_SCRIPTS")           
        ];
    }

}

?>