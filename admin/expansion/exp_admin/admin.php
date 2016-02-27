<?php defined('_CHECK_') or die("Access denied");

$controller = FrontController::instance();


$controller->loadController();

echo $controller->run();

?>