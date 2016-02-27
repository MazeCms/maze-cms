<?php

namespace ui\role;

use ui\select\Chosen;

class Roles extends Chosen {

    public function init() {
        parent::init();
        $this->items = \maze\table\Roles::getList();
    }

}
