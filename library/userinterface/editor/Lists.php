<?php

namespace ui\editor;

use maze;
use ui\select\Chosen;
use maze\helpers\ArrayHelper;

class Lists extends Chosen {

    public $front = 0;
    
    public function init() {
        parent::init();
        $result = \RC::getDb()->cache(function($db){
                return maze\table\Plugin::find()
                    ->from(['p'=>maze\table\Plugin::tableName()])    
                    ->innerJoinWith('installApp')
                    ->andOnCondition(['ia.front_back' =>$this->front])
                    ->andWhere(['p.group_name'=>'editor', 'p.enabled'=>1])                  
                    ->all();
            }, null, 'fw_ui');
        if($result)
        {
            $this->items =  ArrayHelper::map($result, 'name', 'name'); 
        }
    }

}
