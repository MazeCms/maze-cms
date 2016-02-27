<?php 
namespace tmp\defaults;

class Install extends \maze\install\TmpInstall
{
	public $name = 'defaults';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("TMP_DEFAULTS_STEP_INSTALL_INIT"),
            'copy' => $this->text("TMP_DEFAULTS_STEP_INSTALL_COPY"),
            'add' => $this->text("TMP_DEFAULTS_STEP_INSTALL_ADD"),
            'remove' => $this->text("TMP_DEFAULTS_STEP_INSTALL_REMOVE")
        ];
    }
	
}

?>