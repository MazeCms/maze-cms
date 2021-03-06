<?php 
namespace plg\system\compressor;

class Install extends \maze\install\PlgInstall
{
    public $name = 'compressor';

    public $group = 'system';

    public $enabled = 1;
    
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