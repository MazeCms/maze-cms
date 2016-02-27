<?php 
namespace wid\wid_formsend;

class Install extends \maze\install\WidInstall
{
	public $name = 'formsend';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("WID_FORMSEND_CHECK"),
            'copy' => $this->text("WID_FORMSEND_SCRIPTS"),
            'add' => $this->text("WID_FORMSEND_DBADD"),
            'remove' => $this->text("WID_FORMSEND_REMOVETEMP")
        ];
    }
	
}

?>