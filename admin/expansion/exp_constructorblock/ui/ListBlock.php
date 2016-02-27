<?php

namespace admin\expansion\exp_constructorblock\ui;

use ui\select\Chosen;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;
use admin\expansion\exp_constructorblock\table\Block;

class ListBlock extends Chosen {
    
    public $condition = [];

    public function init() {
  
        parent::init();
        $this->items =  ArrayHelper::map(Block::find()->andFilterWhere($this->condition)->asArray()->all(), 'code', 'title'); 
    }

}
