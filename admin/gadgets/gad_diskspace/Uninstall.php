<?php 
namespace gad\gad_diskspace;

class Uninstall extends \maze\install\GadUninstall
{
	public $name = 'diskspace';
	
	public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }
	
}
?>