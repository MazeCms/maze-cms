<?php

namespace ui\date;

use maze;
use ui\select\Chosen;
use maze\helpers\ArrayHelper;

class Lists extends Chosen {

    
    public function init() {
        parent::init();
        $result = \RC::getDb()->cache(function($db){
                return maze\table\Plugin::find()
                    ->from(['p'=>maze\table\Plugin::tableName()])    
                    ->innerJoinWith('installApp')
                    ->andOnCondition(['ia.front_back' =>1])
                    ->andWhere(['p.group_name'=>'calendar', 'p.enabled'=>1])                  
                    ->all();
            }, null, 'fw_ui');
        if($result)
        {
            $this->items =  ArrayHelper::map($result, 'name', 'name'); 
        }
    }

}
