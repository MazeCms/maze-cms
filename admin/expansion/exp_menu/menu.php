<?php


defined('_CHECK_') or die("Access denied");
if (!RC::app()->access->roles("menu", "VIEW_ADMIN"))
    throw new maze\exception\UnauthorizedHttpException(Text::_("EXP_TEMPLATING_ACCESS_DENIED"));

$controller = RC::app()->getController();
$controller->_doc->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
$controller->loadController();
echo $controller->run();
?>