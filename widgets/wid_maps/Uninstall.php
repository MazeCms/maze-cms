<?php 
namespace wid\wid_maps;

class Uninstall extends \maze\install\WidUninstall {

    public $name = "maps";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("WID_MAPS_CHECK"),            
            'del'=>$this->text("WID_MAPS_REMOVETEMP"),
            'remove' => $this->text("WID_MAPS_UNINSTALL")           
        ];
    }

}

?>