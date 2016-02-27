<?php 
namespace exp\exp_dictionary;

class Uninstall extends \maze\install\ExpUninstall {

    public $name = "dictionary";


    public $front = [1,0];

    public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }

}

?>