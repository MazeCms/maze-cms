<?php

namespace ui\term;

use ui\select\Chosen;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;

class Dictionary extends Chosen {

    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(ContentType::find()->where(['expansion'=>'dictionary'])->asArray()->all(), 'bundle', 'title'); 
    }

}
