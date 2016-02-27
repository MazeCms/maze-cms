<?php

use exp\exp_elfinder\table\Profile;

class Elfinderbtn_Plugin_System extends Plugin {

    public function beforeDispatcher() {
        
        $comp = RC::app()->getComponent('elfinder');
        if ($id_role = $this->_access->getIdRole()) {
            $profile = RC::getDb()->cache(function($db) use($id_role) {
             return Profile::find()->from(['ef' => Profile::tableName()])
                            ->joinWith('role')
                            ->where(['er.id_role' => $id_role, 'ef.enabled' => 1])
                            ->orderBy('ef.sort')->exists();
             }, null, "fw_access");
            RC::app()->toolbar->addGroup("system", [
                'class' => 'Buttonset',
                "TITLE" => "EXP_ELFINDER_TITLE",
                "TYPE" => Buttonset::BTNBIG,
                "VISIBLE" => $profile && $this->_access->roles("elfinder", "VIEW_ADMIN"),
                "SORT" => 2,
                "SORTGROUP" => 1,
                "SRC" =>$comp->getUrl( "img/icon-finder.png"),
                "HINT" => ["TITLE" => "PLG_SYSTEM_ELFINDERBTN_BTN_NAME", "TEXT" => "PLG_SYSTEM_ELFINDERBTN_BTN_DES"],
                "ACTION" => "cms.loadFileManager(); return false;"
                    ]
            );
        }
    }

}

?>