<?php

namespace plg\system\replaceterm\ui;

use ui\select\Chosen;
use maze\table\FieldExp;
use maze\helpers\ArrayHelper;

class AllFields extends Chosen {

    public function init() {
        parent::init();
        $this->items =  ArrayHelper::map(FieldExp::find()->groupBy('field_name')->asArray()->all(), 'field_name', function($data){
            return $data['title'].' ['.$data['field_name'].']';
        }); 
    }

}
