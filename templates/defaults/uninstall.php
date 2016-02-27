<?php 
namespace tmp\defaults;

class Uninstall extends \maze\install\TmpUninstall {

    public $name = "defaults";

    public $front = 1;

    public function getCommands() {
        return [
            'init' => $this->text("TMP_DEFAULTS_STEP_UNINSTALL_INIT"),            
            'del'=>$this->text("TMP_DEFAULTS_STEP_UNINSTALL_DEL"),
            'remove' => $this->text("TMP_DEFAULTS_STEP_UNINSTALL_REMOVE")           
        ];
    }

}
?>