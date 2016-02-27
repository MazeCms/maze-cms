<?php

defined('_CHECK_') or die("Access denied");

class Installer {

    /**
     * @var int - установка
     */
    const INSTALL = 1;
    
    /**
     * @var int - деинсталяция
     */
    const UNINSTALL = 0;
    
    const SITE = 1;
    
    const ADMIN = 0;

    private static $_instance;

    /**
     * @var object - экземпляр класса инсталятора или деинстолятора 
     */
    protected $objInstall;

    /**
     * @var string - путь где хранятся временные файлы пакета уставноки
     */
    public $dir = '@root/temp/install';

    /**
     * @var int - режим установки  INSTALL | UNINSTALL
     */
    public $mode;

    /**
     * @var string - имя пакера расширения 
     */
    public $name;

    /**
     * @var string -  имя типа расширения 
     */
    public $type;

    /**
     * @var stirng -  группа плагина или языка
     */
    public $group;

    /**
     * @var int -  принадлежность расширения
     */
    public $front;
    
    public static $typeInstall = [
        'expansion' => 'maze\install\ExpInstall',
        'widget' => 'maze\install\WidInstall',
        'plugin' => 'maze\install\PlgInstall',
        'languages' => 'maze\install\LanInstall',
        'library' => 'maze\install\LibInstall',
        'gadget' => 'maze\install\GadInstall',
        'template' => 'maze\install\TmpInstall'
    ];
    public static $typeUninstall = [
        'expansion' => 'maze\install\ExpUninstall',
        'widget' => 'maze\install\WidUninstall',
        'plugin' => 'maze\install\PlgUninstall',
        'languages' => 'maze\install\LanUninstall',
        'library' => 'maze\install\LibUninstall',
        'gadget' => 'maze\install\GadUninstall',
        'template' => 'maze\install\TmpUninstall'
    ];

    protected function __construct() {
        
    }

    public static function instance($properties) {
        if (!isset(static::$_instance[$properties['name']])) {
            static::$_instance[$properties['name']] = new static();
            RC::configure(static::$_instance[$properties['name']], $properties)->init();
        }
        return self::$_instance[$properties['name']];
    }

    public function init() {
        if ($this->mode == self::INSTALL) {

            $suffix = [
                'expansion' => 'exp',
                'widget' => 'wid',
                'plugin' => 'plg',
                'languages' => 'lan',
                'library' => 'lib',
                'gadget' => 'gad',
                'template' => 'tmp'
            ];

            $path = RC::getAlias($this->dir);

            if ($this->type == 'plugin') {
                $path .= DS . $suffix[$this->type] . '_' . $this->group . '_' . $this->name;
            } else {
                $path .= DS . $suffix[$this->type] . '_' . $this->name;
            }


            $path .= DS . "Install.php";

            if (!file_exists($path)) {
                throw new Exception('Отсутвует файл установки -' . $path);
            }

            include($path);


            $className = $this->getClassName();


            if (!class_exists($className)) {
                throw new Exception('Отсутвует класс установки -' . $className);
            }

            if (!isset(static::$typeInstall[$this->type]) || get_parent_class($className) !== static::$typeInstall[$this->type]) {
                throw new Exception('Класс установки не наследует - ' . $className);
            }

            $this->objInstall = RC::createObject(['class' => $className, 'path' => RC::getAlias($this->dir)]);
        } elseif ($this->mode == self::UNINSTALL) {
            switch ($this->type) {
                case "expansion":
                    $path = PATH_ADMINISTRATOR . DS . "expansion" . DS . "exp_" . $this->name;
                    break;

                case "widget":
                    $path = $this->front ? PATH_ROOT : PATH_ADMINISTRATOR;
                    $path .= DS . "widgets" . DS . "wid_" . $this->name;
                    break;

                case "template":
                    $path = $this->front ? PATH_ROOT : PATH_ADMINISTRATOR;
                    $path .= DS . "templates" . DS . $this->name;
                    break;

                case "gadget":
                    $path = PATH_ADMINISTRATOR . DS . "gadgets" . DS . "gad_" . $this->name;
                    break;

                case "plugin":
  
                    $path = PATH_ROOT . DS . "plugins" . DS . $this->group . DS . $this->name;
                    break;

                case "library":
                    $path = PATH_ROOT . DS . "library" . DS . $this->name;
                    break;

                case "languages":
                    $path = PATH_ROOT . DS . "language";
                    break;

                default:
                    return false;
                    break;
            }
            if($this->type == 'languages'){
                $path .= DS .$this->name.($this->front ? '.site.' : '.admin.')."Uninstall.php";
            }else{
                $path .= DS . "Uninstall.php";
            }
            

            if (!file_exists($path)) {
                throw new Exception('Отсутвует файл деинсталяции -' . $path);
            }

            include($path);


            $className = $this->getClassName();


            if (!class_exists($className)) {
                throw new Exception('Отсутвует класс деинсталятора -' . $className);
            }

            if (!isset(static::$typeUninstall[$this->type]) || get_parent_class($className) !== static::$typeUninstall[$this->type]) {
                throw new Exception('Класс деинсталятора не наследует - ' . $className);
            }

            $this->objInstall = RC::createObject([
                'class' => $className
            ]);
        }
    }

    public function getClassName() {
        switch ($this->type) {
            case "expansion":
                $name = 'exp\exp_' . $this->name;
                break;

            case "widget":
                $name = 'wid\wid_' . $this->name;
                break;
            
            case "template":
                $name = 'tmp\\' . $this->name;
                break;

            case "plugin":
                $name = 'plg\\' . $this->group . '\\' . $this->name;
                break;

            case "languages":
                $name = 'lan\\' . str_replace('-', '_', $this->name);
                break;

            case "library":
                $name = 'lib\\' . $this->name;
                break;

            case "gadget":
                $name = 'gad\gad_' . $this->name;
                break;
        }

        $name .= $this->mode == self::INSTALL ? '\Install' : '\Uninstall';
        return $name;
    }

    public function getCommands() {

        return $this->objInstall->getCommands();
    }

    public function getErrors($cmd = null) {
        return $this->objInstall->getErrors($cmd);
    }

    public function hasErrors($cmd = null) {
        return $this->objInstall->hasErrors($cmd);
    }

    public function exec($cmd) {
        return $this->objInstall->exec($cmd);
    }

}

?>