<?php defined('_CHECK_') or die("Access denied");

if (!RC::app()->access->roles("elfinder", "VIEW_ADMIN"))
    throw new Exception(Text::_("EXP_ELFINDER_ACCESS_DENIED"));

$controller = RC::app()->getController();
$controller->loadController();

if (!($controller->request->isAjax() && $controller->request->get('clear') == 'ajax') &&  
        RC::app()->access->roles("elfinder", "VIEW_PATH")) {
    $profile = exp\exp_elfinder\table\Profile::find()->all();

    foreach ($profile as $pr) {

        RC::app()->getMenu()->addItems('menu-elfinder-1', [
            'id' => 'menu-elfinder-item-' . $pr->profile_id,
            'title' => $pr->title,
            'path' => ['/admin/elfinder/path', ['profile_id' => $pr->profile_id]],
            'active' => ($controller->request->get('profile_id') == $pr->profile_id)
        ]);
    }
}



echo $controller->run();
?>