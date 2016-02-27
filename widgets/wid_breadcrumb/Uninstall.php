<?php 
namespace wid\wid_breadcrumb;

class Uninstall extends \maze\install\WidUninstall {

    public $name = "breadcrumb";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("Инициализация"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("Удаляю скрипты")           
        ];
    }

}

?>