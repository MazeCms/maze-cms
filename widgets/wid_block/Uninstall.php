<?php 
namespace wid\wid_block;

class Uninstall extends \maze\install\WidUninstall {

    public $name = "block";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("WID_BLOCK_CHECK"),            
            'del'=>$this->text("WID_BLOCK_REMOVETEMP"),
            'remove' => $this->text("WID_BLOCK_UNINSTALL")           
        ];
    }

}

?>