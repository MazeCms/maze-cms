<?php

namespace admin\expansion\exp_sitemap\ui;

use ui\select\Chosen;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;
use admin\expansion\exp_sitemap\table\Sitemap;

class SiteMapList extends Chosen {

    public $condition = [];
    
    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(Sitemap::find()->andFilterWhere($this->condition)->asArray()->all(), 'sitemap_id', 'title'); 
    }

}
