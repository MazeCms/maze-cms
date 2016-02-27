<?php 
namespace plg\contents\sitemapupdate;

class Install extends \maze\install\PlgInstall
{
    public $name = 'sitemapupdate';

    public $group = 'contents';

    public $enabled = 1;
    
    public $front = 0;

    public $lang = ["ru-RU"];                  
    
    public $defaultLang = "ru-RU";
    

    public function getCommands() {
        return [
            'init' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_INIT"),
            'copy' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_COPY"),
            'add' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_ADD"),
            'remove' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_REMOVE")
        ];
    }
    
}

?>