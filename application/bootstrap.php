<?php

defined('_CHECK_') or die("Access denied");

if (get_magic_quotes_gpc()) {
    @ini_set('magic_quotes_gpc', 'off');
}
if (get_magic_quotes_runtime()) {
    @ini_set('magic_quotes_runtime', 'Off');
}

if(!file_exists(PATH_ROOT . '/configuration.php')){
    header('Location: /install/install');
    exit();
}

require_once PATH_SITE . '/configuration.php';


include_once (PATH_LIBRARIES . DS . "framework" . DS . "class.rc.php");

$config = new Config();

switch ($config->error_reporting) {
    case 'none':
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_STRICT);
        ini_set('display_errors', 0);
        $path = RC::getAlias(trim($config->path_log, "\/"));
        if (empty($path) || !is_dir($path)) {
            $path = PATH_SITE . DS . 'temp' . DS . 'log';
        }

        $path .= DS . 'errors_' . date('d-m-Y') . '.log';

        ini_set('error_log', $path);
        ini_set('log_errors', 1);
        break;

    case 'simple':
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        ini_set('display_errors', 1);
        ini_set('log_errors', 0);
        break;

    case 'maximum':
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('log_errors', 0);
        break;

    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        ini_set('log_errors', 0);
        break;

    default:
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 1);
        ini_set('log_errors', 0);
        break;
}

define('DEBUG', $config->debug);

setlocale(LC_ALL, str_replace("-", "_", $config->language) . "." . $config->charset); // Языковая настройка.

date_default_timezone_set($config->timezone);

unset($config);


/*
 * ПОДКЛЮЧАЕМ ПРИЛОЖЕНИЯ И ЗАГРУЖАЕМ БИБЛИОТЕКИ
 */
RC::getClass(); // авто загрузка классов

RC::getErrorHandler()->register(); //регистрируем обработчик ошибок

$app = RC::app()->dispatcher(); 

echo $app;
?>