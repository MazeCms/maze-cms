<?php defined('_CHECK_') or die("Access denied");

$path = explode(DS,PATH_SITE);
array_pop($path);

define('ADMIN', 1);
define('PATH_ROOT',          implode(DS,$path));
define('PATH_CONFIGURATION', PATH_SITE.DS.'application');
define('PATH_ADMINISTRATOR', PATH_ROOT.DS.'admin');
define('PATH_LIBRARIES',     PATH_ROOT.DS.'library');
define('PATH_EXPANSION',     PATH_SITE.DS.'expansion');
define('PATH_THEMES',        PATH_SITE.DS.'templates');
define('PATH_PUGINS',        PATH_ROOT.DS.'plugins');
define('PATH_CACHE',         PATH_SITE.DS.'temp'.DS.'cache');
define('PATH_WIDGET',        PATH_SITE.DS.'widgets');
define('PATH_UI',           PATH_LIBRARIES.DS.'userinterface');
?>