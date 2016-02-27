<?php

namespace ui\meta;

use ui\select\Chosen;

class Robots extends Chosen {

    public function init() {
        parent::init();
        $this->items = [
            "index, follow" => "Index, Follow",
            "noindex, follow" => "No index, follow",
            "index, nofollow" => "Index, No follow",
            "noindex, nofollow" => "No index, no follow"
        ];
    }

}
