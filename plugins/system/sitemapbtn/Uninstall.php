<?php 
namespace plg\system\sitemapbtn;

class Uninstall extends \maze\install\PLgUninstall
{
	public $name = 'sitemapbtn';

    public $group = 'system';
	
	public $front = 0;

	public function getCommands() {
        return [
            'init' => $this->text("PLG_SYSTEM_SITEMAPBTN_INSTALL_INIT"),
            'del' => $this->text("PLG_SYSTEM_SITEMAPBTN_INSTALL_DEL"),
            'remove' => $this->text("PLG_SYSTEM_SITEMAPBTN_UNINSTALL_REMOVE")      
        ];
    }
	
}

?>