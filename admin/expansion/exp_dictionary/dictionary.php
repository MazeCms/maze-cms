<?php
defined('_CHECK_') or die("Access denied");
if (!RC::app()->access->roles("dictionary", "VIEW_ADMIN"))
    throw new maze\exception\UnauthorizedHttpException(Text::_("LIB_FRAMEWORK_DOCUMENT_ACCESS_DENIED"));

$controller = RC::app()->getController();
$controller->loadController();
if (!($controller->request->isAjax() && $controller->request->get('clear') == 'ajax')) {
    $type = maze\table\ContentType::find()->where(['expansion'=>'dictionary'])->all();

    foreach ($type as $t) {

        RC::app()->getMenu()->addItems('menu-dictionary-field', [
            'id' => 'menu-dictionary-field' . $t->bundle,
            'title' => $t->title,
            'path' => ['/admin/dictionary/field', ['run'=>'field', 'bundle' => $t->bundle]],
            'active' => (RC::app()->router->run == 'field' && $controller->request->get('bundle') == $t->bundle),
        ]);
        
        RC::app()->getMenu()->addItems('menu-dictionary-term', [
            'id' => 'menu-dictionary-term' . $t->bundle,
            'title' => $t->title,
            'path' => ['/admin/dictionary/term', ['run'=>'term', 'bundle' => $t->bundle]],
            'active' => (RC::app()->router->run == 'term' && $controller->request->get('bundle') == $t->bundle),
        ]);
    }
}
echo $controller->run();
?>