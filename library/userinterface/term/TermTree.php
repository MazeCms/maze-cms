<?php

namespace ui\term;

use ui\menu\SelectTree;
use maze\table\DictionaryTerm;
use maze\db\Query;

class TermTree extends SelectTree {

    public $condition = [];
    public $bundle;
    public $disable;

    public function init() {


        $this->items = [];
        $terms = DictionaryTerm::find()->joinWith(['fields'])
                        ->where(['dt.bundle' => $this->bundle])
                        ->andFilterWhere($this->condition)
                        ->from(['dt' => DictionaryTerm::tableName()])
                        ->orderBy('dt.expansion, dt.bundle, dt.parent, dt.sort')
                        ->all();

        //$this->items[] = ['label' =>'Корень', 'data-icon'=>'/library/image/icons/blue-folder-network-horizontal-open.png', 'value' =>'root'];
        
        foreach ($terms as $item) {
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
                'parent' => ($item->parent > 0 ? $item->parent : 0),
                'disabled' =>($this->disable ? in_array($item->term_id, $this->disable) : false)
            ];
        }

        parent::init();
    }

}
