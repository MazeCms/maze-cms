<?php

namespace exp\exp_contents\ui;

use ui\select\Chosen;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;

class ContentTypeList extends Chosen {

    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(ContentType::find()->where(['expansion'=>'contents'])->asArray()->all(), 'bundle', 'title'); 
    }

}
