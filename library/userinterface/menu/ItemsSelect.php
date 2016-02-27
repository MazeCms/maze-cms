<?php

namespace ui\menu;

use ui\select\Chosen;
use maze\table\MenuGroup;
use maze\helpers\ArrayHelper;

class ItemsSelect extends Chosen {

    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(MenuGroup::find()->asArray()->all(), 'id_group', 'name'); 
    }

}
