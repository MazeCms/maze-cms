<?php 
namespace wid\wid_slidercontents;

class Uninstall extends \maze\install\WidUninstall {

    public $name = "slidercontents";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("WID_SLIDERCONTENTS_CHECK"),            
            'del'=>$this->text("WID_SLIDERCONTENTS_REMOVETEMP"),
            'remove' => $this->text("WID_SLIDERCONTENTS_UNINSTALL")           
        ];
    }

}

?>