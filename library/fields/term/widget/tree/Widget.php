<?php

namespace lib\fields\term\widget\tree;

use RC;
use Text;
use maze\helpers\Html;
use maze\base\JsExpression;
use maze\table\DictionaryTerm;
use maze\db\Query;

class Widget extends \maze\fields\BaseWidget {

    public function run() {
  
        $terms = DictionaryTerm::find()->joinWith(['fields'])
                        ->where(['dt.expansion'=>'dictionary', 'dt.bundle' => $this->field->settings->dictionary])
                        ->from(['dt' => DictionaryTerm::tableName()])
                        ->orderBy('dt.expansion, dt.bundle, dt.parent, dt.sort')
                        ->all();
        
         $value = array_map(function($val){
            return $val->term_id;
            }, $this->data);
        
        $options = [];
        
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
            $options[] = [
                'text' => $result,
                'li_attr' => ['data-id'=>$item->term_id],
                'id'=>'pref-term-'.$item->term_id,
                'icon' => '/library/image/icons/' . ($item->enabled ? 'blue-folder-horizontal.png' : 'lock-warning.png'),
                'parent' => ($item->parent > 0 ? 'pref-term-'.$item->parent : '#'),
                'state' =>['selected'=>(in_array($item->term_id, $value)), 'opened'=>(in_array($item->term_id, $value)) ]
                ];
        }
        $options = [
            'core'=>[
                'data'=>$options,
                'strings'=>['Loading ...'=>'Загрузка...'],
                'multiple'=>true
                ],
            'checkbox'=>['three_state'=>false],
            'plugins'=>["wholerow", "checkbox"]
            
        ];

        
        return  $this->render('index', [
                'widget'=>$this, 
                'form'=>$this->form,
                'settings'=>$this->field->settings,
                'options'=>$options
              ]);
        
    }

}
