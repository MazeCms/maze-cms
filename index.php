<?php

if (version_compare(PHP_VERSION, '5.6') < 0) {
  print 'Your PHP installation is too old. MAZE CMS requires at least PHP 5.6';
  exit;
}
define('_CHECK_', 1);

define('DS', DIRECTORY_SEPARATOR);
/**
 * Полный путь к корню директории 
 */
define('PATH_ROOT', dirname(__FILE__));

if (file_exists(PATH_ROOT . DS . 'application' . DS . 'defines.php')) {
    include_once PATH_ROOT . DS . 'application' . DS . 'defines.php';
}

/*
 *   Подключаем бутстрап
 */
if (defined('PATH_CONFIGURATION')) {
    include_once PATH_CONFIGURATION . DS . 'bootstrap.php';
}
?>