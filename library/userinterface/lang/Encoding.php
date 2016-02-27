<?php

namespace ui\lang;

use ui\select\Chosen;
use maze\table\Languages;

class Encoding extends Chosen {

    public function init() {
        parent::init();
        $path = PATH_LIBRARIES . DS . "base" . DS . 'encoding.xml';
        if (!file_exists($path))
            return false;

        $obj = simplexml_load_file($path);

        $option = [];

        foreach ($obj as $val) {
            $option["$val->value"] = $val->value;
        }


        $this->items = $option;
    }

}
