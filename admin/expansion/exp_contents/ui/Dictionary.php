<?php

namespace exp\exp_contents\ui;

use ui\select\Chosen;
use maze\table\ContentType;
use maze\helpers\ArrayHelper;
use maze\table\FieldExp;

class Dictionary extends Chosen {

    public function init() {
        parent::init();
        
        $catalog = FieldExp::find()
                    ->from(['fe' => FieldExp::tableName()])
                    ->joinWith(['typeFields'])
                    ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
                    ->all();
        
        $bundleTerm = [];
        foreach ($catalog as $cat) {
            if (isset($cat->param['dictionary'])) {
                if (in_array($cat->param['dictionary'], $bundleTerm)) {
                    continue;
                }
                $bundleTerm[] = $cat->param['dictionary'];
            }
        }
        
        $this->items =  ArrayHelper::map(ContentType::find()->where(['expansion'=>'dictionary', 'bundle'=>$bundleTerm])->asArray()->all(), 'bundle', 'title'); 
    }

}
