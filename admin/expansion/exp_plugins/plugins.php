<?php defined('_CHECK_') or die("Access denied");
if(!RC::app()->access->roles("plugins", "VIEW_ADMIN")) throw new Exception(Text::_("LIB_FRAMEWORK_DOCUMENT_ACCESS_DENIED"));


$controller = RC::app()->getController();

$controller->loadController();
echo $controller->run();
?>