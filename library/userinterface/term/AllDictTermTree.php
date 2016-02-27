<?php

namespace ui\term;

use ui\menu\SelectTree;
use maze\table\DictionaryTerm;
use maze\table\ContentType;
use maze\db\Query;
use maze\table\FieldExp;

class AllDictTermTree extends SelectTree {

    public $condition = [];
    public $disable;

    public function init() {

 
        $this->items = [];
        $terms = DictionaryTerm::find()->joinWith(['fields'])
                        ->where(['dt.expansion'=>'dictionary'])
                        ->andFilterWhere($this->condition)
                        ->from(['dt' => DictionaryTerm::tableName()])
                        ->orderBy('dt.expansion, dt.bundle, dt.parent, dt.sort')                   
                        ->all();

  
        $bundle = []; 
        
        foreach ($terms as $item) {
            if(!in_array($item->bundle, $bundle)){
                $bundle[] = $item->bundle;
            }
            $fields = $item->fields;
            $result = null;
            foreach ($fields as $fl) {
                if ($fl->field_name == 'title') {
                    $field = (new Query())->from("{{%field_title_title}}")
                            ->where(['entry_id' => $item->term_id, 'field_exp_id' => $fl->field_exp_id])
                            ->one();
                    $result = $field['title_value'];
                    break;
                }
            }
            $this->items[] = [
                'label' => $result,
                'value' => $item->term_id,
                'data-icon' => '/library/image/icons/' . ($item->enabled ? 'blue-folder-horizontal.png' : 'lock-warning.png'),
                'parent' =>  ($item->parent > 0 ? $item->parent : $item->bundle),
                'disabled' =>($this->disable ? in_array($item->term_id, $this->disable) : false)
            ];
        }
       
        foreach ($bundle as $b) {
            $type = ContentType::findOne(['expansion'=>'dictionary', 'bundle'=>$b]);
            $title = $type ? $type->title : $b;
            $this->items[] = ['label' =>$title, 'disabled'=>true, 'data-icon'=>'/library/image/icons/blue-folder-network-horizontal-open.png', 'value' =>$b, 'parent'=>'#'];
        }

        parent::init();
    }

}
