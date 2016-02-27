<?php 
namespace wid\wid_slidercontents;

class Install extends \maze\install\WidInstall
{
	public $name = 'slidercontents';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("WID_SLIDERCONTENTS_CHECK"),
            'copy' => $this->text("WID_SLIDERCONTENTS_SCRIPTS"),
            'add' => $this->text("WID_SLIDERCONTENTS_DBADD"),
            'remove' => $this->text("WID_SLIDERCONTENTS_REMOVETEMP")
        ];
    }
	
}

?>