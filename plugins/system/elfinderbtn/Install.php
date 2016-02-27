<?php 
namespace plg\system\elfinderbtn;

class Install extends \maze\install\PLgInstall
{
    public $name = 'elfinderbtn';

    public $group = 'system';

    public $enabled = 1;
    
    public $front = 0;

    public $lang = ["ru-RU", "en-GB"];                  
    
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