<?php

namespace plg\contents\sitemapupdate;

class Uninstall extends \maze\install\PlgUninstall {

    public $name = 'sitemapupdate';
    public $group = 'contents';
    public $front = 0;

    public function getCommands() {
        return [
            'init' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_INIT"),
            'del' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_INSTALL_DEL"),
            'remove' => $this->text("PLG_CONTENTS_SITEMAPUPDATE_UNINSTALL_REMOVE")
        ];
    }

}

?>