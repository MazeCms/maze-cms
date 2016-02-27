<?php

defined('_CHECK_') or die("Access denied");
if (!RC::app()->access->roles("contents", "VIEW_ADMIN"))
    throw new maze\exception\UnauthorizedHttpException(Text::_("LIB_FRAMEWORK_DOCUMENT_ACCESS_DENIED"));

$controller = RC::app()->getController();

if (!($controller->request->isAjax() && $controller->request->get('clear') == 'ajax')) {
    
    // типы материалов
    $type = maze\table\ContentType::find()->where(['expansion' => 'contents'])->all();

    foreach ($type as $t) {

        RC::app()->getMenu()->addItems('menu-contents-field', [
            'id' => 'menu-contents-field-' . $t->bundle,
            'title' => $t->title,
            'path' => ['/admin/contents/field', ['run' => 'field', 'bundle' => $t->bundle]],
            'active' => (RC::app()->router->run == 'field' && $controller->request->get('bundle') == $t->bundle),
        ]);
    }
    
    // каталог материалов
    $catalog = maze\table\FieldExp::find()
            ->from(['fe' => maze\table\FieldExp::tableName()])
            ->joinWith(['typeFields'])
            ->where(['fe.expansion' => 'contents', 'f.type' => 'term'])
            ->all();
    $dicHas = [];
    foreach ($catalog as $cat) {
        if (isset($cat->param['dictionary'])) {
            // отбираем только уникальные словари
            if (in_array($cat->param['dictionary'], $dicHas)){
                
                continue;
            }
                
            $dicHas[] = $cat->param['dictionary'];
            $dictionary = maze\table\ContentType::find()
                    ->from(['ct'=>maze\table\ContentType::tableName()])
                    ->joinWith(['term'=>function($q){$q->orderBy('dt.parent, dt.sort')->groupBy('dt.term_id');}, 'term.fields'])
                    ->where(['ct.expansion' => 'dictionary', 'ct.bundle' => $cat->param['dictionary']])
                    ->one();            
            RC::app()->getMenu()->addItems('menu-contents-catalog', [
                'id' => 'menu-contents-catalog-' . $dictionary->bundle,
                'title' => $dictionary->title,
                'path' => ['/admin/contents/catalog', ['run'=>'dict', 'bundle' => $dictionary->bundle]],
                'active' => (RC::app()->router->controller == 'catalog' && $controller->request->get('bundle') == $dictionary->bundle && !$controller->request->get('term_id')),
            ]);

            $result = [];
            foreach ($dictionary->term as $t) {
                $fields = $t->fields;
                $title = null;
                foreach ($fields as $fl) {
                    if ($fl->field_name == 'title') {
                        $field = (new maze\db\Query())->from("{{%field_title_title}}")
                                ->where(['entry_id' => $t->term_id, 'field_exp_id' => $fl->field_exp_id])
                                ->one();
                        $title = $field['title_value'];
                        break;
                    }
                }

                $parent = $t->parent > 0 ? 'menu-contents-catalog-term-'.$t->parent : 'menu-contents-catalog-'.$dictionary->bundle;
                
                 RC::app()->getMenu()->addItems($parent, [
                'id' => 'menu-contents-catalog-term-'.$t->term_id,
                'title' => $title,
                'path' => ['/admin/contents/catalog', ['run'=>'term', 'bundle'=>$dictionary->bundle, 'term_id'=>$t->term_id]],
                'active' => (RC::app()->router->controller == 'catalog' && $controller->request->get('term_id') == $t->term_id),
                ]);
                 
               
            }
        }
    }

}

$controller->loadController();
echo $controller->run();


?>