<?php

defined('_CHECK_') or die("Access denied");

use maze\base\Container;
use maze\db\Connection;
use maze\helpers\FileHelper;

abstract class RC {

    const ROUTERSITE = 1;
    const ROUTERADMIN = 0;
    const ROUTERINSTALL = 2;

    private static $config;
    
    private static $conf;
    
    private static $router;
    
    private static $application;
    
    private static $loadClass;
    
    private static $mail;
    
    private static $cahe = [];
    
    private static $plugin = [];
    
    private static $menu;

    private static $connect = [];
    
    private static $error;
    
    private static $logger;
    
    public static $aliases = [
        '@maze' => __DIR__,
        '@admin' => PATH_ADMINISTRATOR,
        '@exp' => PATH_EXPANSION,
        '@lib' => PATH_LIBRARIES,
        '@ui' => PATH_UI,
        '@root' => PATH_ROOT,
        '@site' => PATH_SITE,
        '@plg' => PATH_PUGINS,
        '@wid' => PATH_WIDGET,
        '@lan' => PATH_ROOT . DS . "language",
        '@gad' => PATH_ADMINISTRATOR . DS . "gadgets",
        '@tmp' => PATH_THEMES
    ];
    public static $container;
    
    public static function setAlias($alias, $path){
        static::$aliases[$alias] = $path;
    }

    public static function getAlias($alias, $throwException = true) {
        if (strncmp($alias, '@', 1)) {

            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            } else {
                foreach (static::$aliases[$root] as $name => $path) {
                    if (strpos($alias . '/', $name . '/') === 0) {
                        return $path . substr($alias, strlen($name));
                    }
                }
            }
        }

        if ($throwException) {
             throw new maze\exception\UserException("Данного пути не существует, с алисом: $alias");
        } else {
            return false;
        }
    }

    public static function createObject($type, array $params = []) {
        if (static::$container == null) {
            static::$container = new Container;
        }

        if (is_string($type)) {
            return static::$container->get($type, $params);
        } elseif (is_array($type) && isset($type['class'])) {
            $class = $type['class'];
            unset($type['class']);

            return static::$container->get($class, $params, $type);
        } elseif (is_callable($type, true)) {
            return call_user_func($type, $params);
        } elseif (is_array($type)) {
            throw new maze\exception\UserException('Конфигурация объекта должна содежать массив с ключем "class" и именем класса');
        } else {
            throw new maze\exception\UserException("Не поддерживаемый тип конфигурации: " . gettype($type));
        }
    }

    public static function configure($object, $properties) {
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    public static function getDb($name = 'default') {
        $conf = RC::getConfig();

        if (isset($conf->database[$name])) {
            if (isset(self::$connect[$name]))
                return self::$connect[$name];

            $dbConf = $conf->database[$name];
            return self::$connect[$name] = new Connection([
                "dsn" => $dbConf["type"] . ":host=" . $dbConf["host"] . ";dbname=" . $dbConf["bdname"],
                "username" => $dbConf["user"],
                "password" => $dbConf["password"],
                "charset" => $dbConf["encoding"],
                "tablePrefix" => $dbConf["dbprefix"] . "_",
                "queryCacheDuration" => (int) $conf->time_cache
            ]);
            
       
        }
        else {
            throw new maze\exception\UserException("данной базы данных $name не существует", 500);
        }
    }

    public static function getConfig() {
        if (self::$config == null) {
            $pach = PATH_ROOT . DS . 'configuration.php';
            if (file_exists($pach)) {
                include_once ($pach);
                $classname = "Config";
                if (class_exists($classname)) {
                    self::$config = new Registry(new $classname());
                }
            }
        }
        return self::$config;
    }

    public static function getClass() {
        if (self::$loadClass == null) {
            include_once (PATH_LIBRARIES . DS . "framework" . DS . "load" . DS . "class.classautoloader.php");

            self::$loadClass = new ClassAutoloader(PATH_LIBRARIES);
        }
        return self::$loadClass;
    }
    
    public static function getErrorHandler(){
        if(static::$error == null){
           static::$error = static::createObject(['class'=>'maze\debug\ErrorHandler']);
        }
        return static::$error;
    }

    public static function getRouter($router = null) {
        $router = $router !== null ? $router : (defined("SITE") ? 1 : (defined("ADMIN") ? 0 : 2));
        
        if (!isset(static::$router[$router])) {
            if ($router == RC::ROUTERSITE) {
                $classname = "SiteRouter";
                $path = "@root/application";
            } elseif ($router == RC::ROUTERADMIN) {
                $classname = "AdminRouter";
                $path = "@admin/application";
            } else {
                $classname = "InstallRouter";
                $path = "@root/install/application";
            }            
            $path = RC::getAlias($path.'/router.php');
            if (file_exists($path)) {
                include_once ($path);

                if (class_exists($classname)) {
                    $refClass = new ReflectionClass($classname);

                    if ($refClass->isSubclassOf('Router')) {
                       
                        static::$router[$router] = new $classname();
                    }
                }
            }
        }
        return static::$router[$router];
    }

    public static function getCache($type) {
        
        $conf = RC::getConfig();
        
        if (!isset(self::$cahe[$type])) {
            $options = ['class'=>'\maze\cache\FileCache', 'type'=>$type];
            if($conf){
                if($conf->get('type_cache') && $conf->get('type_cache') == 'memcache' && extension_loaded('memcached')){
                    $options['class'] = '\maze\cache\MemCache';
                    
                    if($conf->get('memcache_username')){
                        $options['username'] = $conf->get('memcache_username');
                    }
                    
                    if($conf->get('memcache_password')){
                        $options['password'] = $conf->get('memcache_password');
                    }
                    
                    if($conf->get('memcache_host') && $conf->get('memcache_port')){
                        $options['servers'][] = [
                            'host'=>$conf->get('memcache_host'),
                            'port'=>$conf->get('memcache_port')
                        ];
                    }
                    
                    
                    
                }
                
                $options['time'] = $conf->get('time_cache');
                $options['enable'] = $conf->get('enable_cache');
            }
           
            
            self::$cahe[$type] =  RC::createObject($options); 
          
        }
        return self::$cahe[$type];
    }
    
    public static function getApplication() {
        return self::app();
    }

    public static function app() {
        if (static::$application == null) {
            if (defined("SITE")) {
                $classname = "SiteApp";
            } elseif (defined("ADMIN")) {
                $classname = "AdminApp";
            } else {
                $classname = "InstallApp";
            }
            

            $pach = PATH_CONFIGURATION . DS . 'application.php';
            
            if (file_exists($pach)) {
                include_once ($pach);

                if (class_exists($classname)) {
                    $refClass = new ReflectionClass($classname);

                    if ($refClass->isSubclassOf('Application')) {
                        static::$application = RC::createObject(['class' => $classname]);
                    }
                }
            }
        }

        return static::$application;
    }

    public static function getPlugin($type) {
        if (!isset(self::$plugin[$type])) {
            $plugin = new LoaderPlugin();
            $plugin->loadPlugin($type);
            self::$plugin[$type] = $plugin;
        }

        return self::$plugin[$type];
    }

    public static function getMail() {
        if (self::$mail == null) {
            RC::import('@lib/swiftmailer/lib/swift_required.php');
            $config = RC::getConfig();
            $mail = RC::createObject([
                'class'=>'maze\mail\Mailer',
                'useFileTransport'=>$config->get('useFileTransport')
             ]);
            
            self::$mail = $mail;
        }
        return self::$mail;
    }

    public static function getMenu() {
        if (!isset(self::$menu)) {
            self::$menu = Menu::instance();
        }
        return self::$menu;
    }

    public static function getConf($arrayset, $val = false) {

        $hash = md5(serialize($arrayset));

        if (!isset(self::$conf[$hash])) {
            $front = isset($arrayset['front']) ? $arrayset['front'] : false;

            switch ($arrayset['type']) {
                case "expansion":

                    if (!isset($arrayset['name']))
                        return false;
                    $path = PATH_ADMINISTRATOR . DS . "expansion" . DS . "exp_" . $arrayset['name'];

                    break;

                case "widget":
                    if (!isset($arrayset['name']))
                        return false;
                    $root = $front ? PATH_ROOT : PATH_SITE;
                    $path = $root . DS . "widgets" . DS . "wid_" . $arrayset['name'];

                    break;

                case "template":
                    if (!isset($arrayset['name']))
                        return false;
                    $root = $front ? PATH_ROOT : PATH_SITE;
                    $path = $root . DS . "templates" . DS . $arrayset['name'];
                    
                    break;

                case "plugin":
                    if (!isset($arrayset['name']))
                        return false;
                    if (!isset($arrayset['group']))
                        return false;
                    $path = PATH_ROOT . DS . "plugins" . DS . $arrayset['group'] . DS . $arrayset['name'];

                    break;

                case "gadget":
                    if (!isset($arrayset['name']))
                        return false;
                    $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $arrayset['name'];

                    break;

                case "install":
                    $path = $arrayset["path"];
                    break;

                case "library":

                    break;
            }

            
            $path .= DS . "meta.options.xml";
            
          
            self::$conf[$hash] = new XMLConfig($path, $val);
        }

        return self::$conf[$hash];
    }

    public static function import($path, $options = []) {
        $path = RC::getAlias($path);
        if(is_file($path)){
            include_once($path);
        }else{
            $files = FileHelper::findFiles($path, $options);
            foreach($files as $file){
                include_once($file); 
            }
        }
    }
    
    public static function getLog() {
        if(static::$logger == null){
            $config = [];
            $conf = RC::getConfig();
          
            if($conf){
                $config = [
                'class' => '\maze\log\Logger',
                'path'=>$conf->get('path_log'),
                'enable'=>$conf->get('log_enable'),
                'maxsize'=>$conf->get('log_maxsize'),
                'typeEnable'=>$conf->get('logWrite')
                ];
            }else{               
                $config = ['class' => '\maze\log\Logger', 'enable'=>0];
            }
 
            static::$logger =  RC::createObject($config); 
        }
        return static::$logger;
    }


}

?>