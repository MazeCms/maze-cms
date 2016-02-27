<?php 
namespace gad\gad_onlineuser;

class Install extends \maze\install\GadInstall
{
	public $name = 'onlineuser';
	
	public $front = 1;

	public $lang = ["ru-RU"];					
	
	public $defaultLang = "ru-RU";
	

	public function getCommands() {
        return [
            'init' => $this->text("Проверка"),
            'copy' => $this->text("Установка скриптов"),
            'add' => $this->text("Добавить в базу"),
            'remove' => $this->text("удаление веременных файлов")
        ];
    }
	
}

?>