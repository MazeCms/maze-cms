<?php 
defined('_CHECK_') or die("Access denied");
if (!RC::app()->access->roles("sitemap", "VIEW_ADMIN"))
    throw new maze\exception\UnauthorizedHttpException(Text::_("LIB_FRAMEWORK_DOCUMENT_ACCESS_DENIED"));



$controller = RC::app()->getController();
$controller->loadController();
echo $controller->run();

?>