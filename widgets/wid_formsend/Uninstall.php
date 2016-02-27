<?php 
namespace wid\wid_formsend;

class Uninstall extends \maze\install\WidUninstall {

    public $name = "formsend";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("WID_FORMSEND_CHECK"),            
            'del'=>$this->text("WID_FORMSEND_REMOVETEMP"),
            'remove' => $this->text("WID_FORMSEND_UNINSTALL")           
        ];
    }

}

?>