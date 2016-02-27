<?php 
namespace exp\exp_contents;

class Uninstall extends \maze\install\ExpUninstall {

    public $name = "contents";

    public $sql = ["uninstall.sql"];

    public $front = [1,0];

    public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'sql'=>$this->text("Удаление базы данных приложения Тест"),
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }

}

?>