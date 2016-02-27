<?php 
namespace gad\gad_onlineuser;

class Uninstall extends \maze\install\GadUninstall
{
	public $name = 'onlineuser';
	
	public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }
	
}
?>