<?php

namespace ui\lang;

use ui\select\Chosen;
use maze\table\Languages;

class local extends Chosen {

    public function init() {
        parent::init();
        $path = PATH_LIBRARIES . DS . "base" . DS . 'local.xml';
        if (!file_exists($path))
            return false;

        $obj = simplexml_load_file($path);

        $option = [];

        foreach ($obj as $val) {
            $option["$val"] = $val;
        }


        $this->items = $option;
    }

}
