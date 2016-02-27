<?php

namespace ui\tmp;

use ui\select\Chosen;
use  maze\table\InstallApp;
use maze\helpers\ArrayHelper;

class Template extends Chosen {

    public $condition = [];

    public function init() {
        parent::init();
        $this->items = ArrayHelper::map(InstallApp::find()->where(['type'=>'template'])->asArray()->all(), 'name', 'name');
    }

}
