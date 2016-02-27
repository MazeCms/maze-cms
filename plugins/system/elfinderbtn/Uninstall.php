<?php 
namespace plg\system\elfinderbtn;

class Uninstall extends \maze\install\PLgUninstall
{
	public $name = 'elfinderbtn';

    public $group = 'system';
	
	public $front = 0;

	public function getCommands() {
        return [
            'init' => $this->text("EXP_TEST_CHECK"),            
            'del'=>$this->text("Удаление записей БД"),
            'remove' => $this->text("EXP_TEST_REMOVETEMP")           
        ];
    }
	
}

?>