<?php 
namespace wid\wid_block;

class Install extends \maze\install\WidInstall
{
	public $name = 'block';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("WID_BLOCK_CHECK"),
            'copy' => $this->text("WID_BLOCK_SCRIPTS"),
            'add' => $this->text("WID_BLOCK_DBADD"),
            'remove' => $this->text("WID_BLOCK_REMOVETEMP")
        ];
    }
	
}

?>