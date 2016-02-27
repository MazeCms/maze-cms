<?php

namespace exp\exp_install\model;

use maze\base\Model;
use RC;

class StartInstall extends Model {

    public $serverv;
    public $servern;
    public $safe_mode;
    public $phpv;
    public $pdo;
    public $pdo_mysql;
    public $reflection;
    public $zipArchive;
    public $curl;
    public $simpleXml;
    public $mbstring;
    public $gd;
    public $json;
    public $path = [
        '@root/assets',
        '@root/images',
        '@root/images/thumb',
        '@root/temp',
        '@root/temp/cache',
        '@root/temp/log',
        '@root/temp/mail',
        '@root/temp/session',
        '@root/temp/install',        
        '@root/temp/upload',
        '@root/templates',
        '@root/widgets',
        '@root/plugins',
        '@root/expansion',        
        '@root/admin',
        '@root/admin/expansion',
        '@root/admin/gadgets',
        '@root/admin/temp',
        '@root/admin/templates',
        '@root/admin/widgets'        
    ];

    public function init() {
        $this->servern = $this->getServerName();
        $this->serverv = $this->gerVersionServer() ? $this->gerVersionServer() : true;
        $this->safe_mode = ini_get('safe_mode');
        $this->phpv = $this->getVersionPhp();
        $this->pdo = extension_loaded("PDO") && class_exists('\PDO');
        $this->pdo_mysql = extension_loaded("pdo_mysql");
        $this->reflection = extension_loaded("Reflection") && class_exists('\Reflection');
        $this->zipArchive = extension_loaded("zip") && class_exists('\zipArchive');
        $this->curl = extension_loaded("curl");
        $this->simpleXml = extension_loaded("SimpleXML");
        $this->mbstring = extension_loaded("mbstring");
        $this->gd = extension_loaded("gd");
        $this->json = extension_loaded("json");
    }

    public function rules() {
        return [
            [['servern', 'serverv', 'phpv', 'safe_mode', 'pdo', 'reflection', 'pdo_mysql', 'zipArchive', 'curl', 'simpleXml', 'mbstring', 'gd', 'json'], 'required'],
            ['servern', function($attribute, $params) {
                    if ($this->$attribute < 'apache') {
                        $this->addError($attribute, "Текущий веб сервер не явяется  Apache");
                    }
                }],
            ['serverv', function($attribute, $params) {
                    if (is_numeric($this->$attribute) && $this->$attribute < 2.2) {
                        $this->addError($attribute, "Версия сервера не божет быть мньше 2.2");
                    }
                }],
            ['safe_mode', function($attribute, $params) {
                    if ($this->$attribute) {
                        $this->addError($attribute, "Выключите безопасный режим PHP (php.ini - директива safe_mode)");
                    }
                }],
            ['phpv', function($attribute, $params) {
                    if ($this->$attribute < 5.6) {
                        $this->addError($attribute, "Версия php не может быть меньше 5.6");
                    }
                }],
            ['pdo', 'validateIsTrue', 'params' => ["message" => "Отсутвует класс расширения PDO"]],
            ['pdo_mysql', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения pdo_mysql"]],
            ['reflection', 'validateIsTrue', 'params' => ["message" => "Отсутвует класс расширения Reflection"]],
            ['zipArchive', 'validateIsTrue', 'params' => ["message" => "Отсутвует класс расширения zipArchive"]],
            ['curl', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения curl"]],
            ['simpleXml', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения SimpleXML"]],
            ['mbstring', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения mbstring"]],
            ['gd', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения gd"]],
            ['json', 'validateIsTrue', 'params' => ["message" => "Отсутвует модуль расширения json"]],
            ['path', 'validatePath']
        ];
    }

    public function validatePath($attribute, $params) {
        if ($this->$attribute && is_array($this->$attribute)) {
            foreach ($this->$attribute as $path) {
                if (!$this->getPathRW($path)) {
                    $this->addError($attribute, "Директория (" . $path . ") недоступка для записи");
                    break;
                }
            }
        }
    }

    public function getPathRW($path) {
        return is_writable(RC::getAlias($path)) && is_readable(RC::getAlias($path));
    }

    public function validateIsTrue($attribute, $params) {
        if (!$this->$attribute) {
            $this->addError($attribute, $params['message']);
        }
    }

    public function attributeLabels() {
        return[
            "servern" => "Веб сервер",
            "serverv" => "Версия веб сервера",
            "phpv" => "Версия PHP",
            "pdo" => "Модуль PDO",
            "pdo_mysql" => "Клиенты MySQL расширения PDO",
            "reflection" => "Модуль Reflection - отражения",
            "curl" => "Модуль Curl",
            "zipArchive" => "zipArchive - работа с zip архивами",
            "simpleXml" => "SimpleXML - работа Xml",
            "mbstring" => "mbstring работа с многобайтовыми строками",
            "gd" => "gd - работа с изображениями",
            "json" => "Json строки",
            "path" => "Директории"
        ];
    }

    public function getServerName() {
        $result = null;
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            foreach (['apache', 'nginx', 'lighttpd', 'g-wan', 'gwan', 'iis', 'services', 'development'] as $keyword) {
                if (stripos($_SERVER['SERVER_SOFTWARE'], $keyword) !== false) {
                    $result = $keyword;
                    break;
                }
            }
        }
        return $result;
    }

    public function gerVersionServer() {
        $result = null;
        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            foreach (['apache', 'nginx', 'lighttpd', 'g-wan', 'gwan', 'iis', 'services', 'development'] as $keyword) {
                if (stripos($_SERVER['SERVER_SOFTWARE'], $keyword) !== false) {
                    if (preg_match('/^[a-z-]+\/?([\d+\.?]+).+$/i', $_SERVER['SERVER_SOFTWARE'], $mathes)) {
                        $result = $mathes[1];
                    }
                    break;
                }
            }
        }
        return $result;
    }

    public function getVersionPhp() {
        if (preg_match('/^([\d+\.?]+).+$/i', phpversion(), $mathes)) {
            return $mathes[1];
        }
    }

}
