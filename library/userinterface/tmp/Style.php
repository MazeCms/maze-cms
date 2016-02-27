<?php

namespace ui\tmp;

use ui\select\Chosen;
use  maze\table\Template;
use maze\helpers\ArrayHelper;

class Style extends Chosen {

    public $condition = [];

    public function init() {
        parent::init();
        $this->items = ArrayHelper::map(Template::find()->where($this->condition)->asArray()->all(), 'id_tmp', 'title');
    }

}
