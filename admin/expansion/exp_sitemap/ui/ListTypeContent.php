<?php

namespace admin\expansion\exp_sitemap\ui;

use ui\checkbox\iCheck;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;

class ListTypeContent extends iCheck {
    
    public $condition = [];

    public function init() {
  
        parent::init();
        $this->items =  ArrayHelper::map(ContentType::find()->andFilterWhere($this->condition)->asArray()->all(), 'bundle', 'title'); 
    }

}
