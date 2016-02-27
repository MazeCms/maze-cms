<?php

namespace ui\type;

use ui\select\Chosen;
use maze\helpers\ArrayHelper;
use Text;

class AppList extends Chosen {


    public function init() {
        parent::init();
        $this->items = [
            "expansion" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_EXP"),
            "widget" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_WID"),
            "template" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_TMP"),
            "plugin" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_PLG"),
            "library" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_LIB"),
            "gadget" => Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_GAD"),
            "languages"=>Text::_("LIB_USERINTERFACE_TOOLBAR_TYPE_LANG")
        ];
        
        
    }

}
