<?php 
namespace exp\exp_constructorblock;

class Uninstall extends \maze\install\ExpUninstall {

    public $name = "constructorblock";

    public $front = [0];
    
    public $sql = ['uninstall.sql'];

    public function getCommands() {
        return [
            'init' => $this->text("EXP_CONSTRUCTORBLOCK_CHECK"), 
            'sql'=>$this->text("EXP_CONSTRUCTORBLOCK_UNINSTALLTABLE"), 
            'del'=>$this->text("EXP_CONSTRUCTORBLOCK_DELETEDB"),
            'remove' => $this->text("EXP_CONSTRUCTORBLOCK_DELETE_SCRIPTS")           
        ];
    }

}

?>