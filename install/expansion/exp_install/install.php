<?php
defined('_CHECK_') or die("Access denied");

$controller = RC::app()->getController();
$controller->loadController();
echo $controller->run();
?>