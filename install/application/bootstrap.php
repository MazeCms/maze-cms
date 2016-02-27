<?php

defined('_CHECK_') or die("Access denied");

/**
 * Включаем водщебные кавычки
 * если они выключены
 */
if (get_magic_quotes_gpc()) {
    @ini_set('magic_quotes_gpc', 'off');
}
if (get_magic_quotes_runtime()) {
    @ini_set('magic_quotes_runtime', 'Off');
}



include_once (PATH_LIBRARIES . DS . "framework" . DS . "class.rc.php");

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('log_errors', 0);
define('DEBUG', 1);
setlocale(LC_ALL, "Ru_ru.utf-8");
//
//date_default_timezone_set($config->timezone);

date_default_timezone_set('America/Los_Angeles');


/*
 * ПОДКЛЮЧАЕМ ПРИЛОЖЕНИЯ И ЗАГРУЖАЕМ БИБЛИОТЕКИ
 */
RC::getClass();

RC::getErrorHandler()->register(); 

$app = RC::app()->dispatcher(); 

echo $app;
?>