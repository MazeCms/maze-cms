<?php 
namespace wid\wid_maps;

class Install extends \maze\install\WidInstall
{
	public $name = 'maps';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("WID_MAPS_CHECK"),
            'copy' => $this->text("WID_MAPS_SCRIPTS"),
            'add' => $this->text("WID_MAPS_DBADD"),
            'remove' => $this->text("WID_MAPS_REMOVETEMP")
        ];
    }
	
}

?>