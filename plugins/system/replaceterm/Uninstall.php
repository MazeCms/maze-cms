<?php 
namespace plg\system\replaceterm;

class Uninstall extends \maze\install\PLgUninstall
{
	public $name = 'replaceterm';

    public $group = 'system';
	
	public $front = 1;

	public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }
	
}

?>