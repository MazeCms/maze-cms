<?php

namespace exp\exp_settings\form;

use maze\base\Model;
use maze\helpers\ArrayHelper;
use RC;

class FormSystem extends Model {

    /**
     * @var string - название сайта
     */
    public $site_name;
    
    /**
     * @var array - роль пользователя по умолчанию
     */
    public $role_user;
    
    /**
     * @var boolean - активность сайта
     */
    public $enable_site;
    
    /**
     * @var boolean - показывать сообщение при выключенном сайте
     */
    public $offline_mess;
    
    /**
     * @var string - текс сообщения при выключенном сайте
     */
    public $text_offline;
    
    /**
     * @var string - название плагина редактора админ панели
     */
    public $editor_admin;
    
    /**
     * @var string - название плагина редактора сайта
     */
    public $editor_site;
    
    /**
     * @var string - название плагина календарика сайта
     */
    public $calendar;
    
    /**
     * @var string - название плагина проверочного кода сайта сайта
     */
    public $captcha;
    
    /**
     * @var string - формат даты отображения сайта и админ панели
     */
    public $format_date;
    
    /**
     * @var int - число записей на странице
     */
    public $page_number;
    
    /**
     * @var string - мета данные для поисковиков
     */
    public $meta_robots;
    /**
     * @var string - мета данные для поисковиков
     */
    public $meta_desc;
    /**
     * @var string - мета данные для поисковиков
     */
    public $meta_keys;
    /**
     * @var string - мета данные для поисковиков
     */
    public $meta_author;
    /**
     * @var boolean - показывать мета-тег автора
     */
    public $show_author;
    
    /**
     * @var string - способ отображения ошибок php simple | maximum | none | development | default
     */
    public $error_reporting;
    
     /**
     * @var string - путь к директории с логами
     */
    public $path_log;

    /**
     * @var boolean - включить отладчик
     */
    public $debug;

    /**
     * @var boolean - показывать предварительные стили сайта
     */
    public $viewstyle;

    /**
     * @var boolean - показывать позиции в шаблоне
     */
    public $viewposition;

    /**
     * @var boolean - включить логирование
     */
    public $log_enable;

    /**
     * @var int - максимальный размер файла логов в Мб
     */
    public $log_maxsize;

    /**
     * @var int - время жизни кеша в секундах
     */
    public $time_cache;

    /**
     * @var string - индитефикатор сессии
     */
    public $ses_name;

    /**
     * @var int - время жизни сессии в минутах
     */
    public $ses_time;

    /**
     * @var string - путь к директории где хранятся сессии
     */
    public $ses_path;

    /**
     * @var string - часовой пояс
     */
    public $timezone;

    /**
     * @var string - кодировка  по умолчанию "utf-8"
     */
    public $charset = 'UTF-8';

    /**
     * @var string - языковые аттрибуты "ru-RU"
     */
    public $language;

    /**
     * @var string - Название отправителя, email сайта
     */
    public $fromname;

    /**
     * @var string - email адрес сайта
     */
    public $mailfrom;

    /**
     * @var string - префикс URL дарессов
     */
    public $prefix;

    /**
     * @var boolean - показывать перфикс
     */
    public $enab_prefix;
    
    /**
     * @var boolean - включить кеширование
     */
    public $enable_cache;
    
    /**
     * @var boolean - включить автоматическое определение языка
     */
    public $autolang;

    /**
     * @var boolean - включить сжатие страницы
     */
    public $gzip;
    
    /**
     * @var boolean - отправлять письма или ложить в папку /temp/mail/ 
     */
    public $useFileTransport;
    

    /**
     * @var boolean - включить проверку токена
     */
    public $enableCsrfValidation;

    /**
     * @var array - тип логов для записи 
     */
    public $logWrite;
    
    /**
     * @var string - тип кеширования
     */
    public $type_cache;

    public $memcache_host = '127.0.0.1';
    
    public $memcache_port = 11211;
    
    public $memcache_username;
    
    public $memcache_password;

    public function rules() {
        return [
            [['site_name', 'role_user', 'format_date', 'page_number', 'error_reporting', 
                'path_log', 'log_maxsize', 'time_cache', 'ses_name', 'ses_time', 'ses_path', 
                'charset','language', 'fromname', 'mailfrom', 'logWrite', 'type_cache'], "required"],
            [['enable_site', 'show_author', 'debug', 'viewstyle', 'autolang','gzip', 'viewposition', 
                'log_enable', 'enab_prefix', 'enable_cache', 'offline_mess', 'useFileTransport', 'enableCsrfValidation'], 'boolean'],
            ['log_maxsize', 'number', 'min'=>1, 'max'=>20],
            ['time_cache', 'number', 'min'=>10],
            ['ses_time', 'number', 'min'=>1],
            ['page_number', 'number', 'min'=>1, 'max'=>100],
            ['ses_name', 'match', 'pattern'=>'/^[A-Z]{3,10}$/'],
            ['prefix', 'match', 'pattern'=>'/^[a-z-0-9]{2,10}$/i'],
            ['mailfrom', 'email'],
            ['error_reporting', 'in', 'range'=>['default', 'none', 'simple', 'maximum', 'development']],
            ['meta_robots', 'in', 'range'=>['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow']], 
            [['meta_desc', 'meta_keys', 'meta_author'], 'string'],
            ['language', 'match', 'pattern'=>'/^[a-z]{2,3}-[A-Z]{2}$/'],
            [['text_offline', 'editor_admin', 'editor_site', 'timezone', 'memcache_host', 'memcache_port', 'memcache_username', 'memcache_password'], 'string'],
            ['type_cache', function($attribute, $param){
                if($this->type_cache == 'memcache' && !extension_loaded('memcached')){
                     $this->addError($attribute, \Text::_('EXP_SETTINGS_SERVER_MEMCACHE_NOT'));
                }
            }],
            [['path_log', 'ses_path'],'validPath']
        ];
    }
    
    public function validPath($attribute, $params){
        if($this->hasErrors($attribute)) return false;

        if(!is_dir(RC::getAlias(trim($this->$attribute, '/\\')))){
            $this->addError($attribute, \Text::_('EXP_SETTINGS_SYSTEM_ALER_ERROR_PATH'));
        }
    }
    
  
    public function attributeLabels() {
        return[
            "site_name" => \Text::_("EXP_SETTINGS_SITE_LABEL_NAME"),
            "role_user" => \Text::_("EXP_SETTINGS_SITE_LABEL_ROLE"),
            "enable_site" => \Text::_("EXP_SETTINGS_SITE_LABEL_ENABLE"),
            "offline_mess" => \Text::_("EXP_SETTINGS_SITE_LABEL_OFFLINETEXT"),
            "text_offline" => \Text::_("EXP_SETTINGS_SITE_LABEL_TEXTDISABLE"),
            "editor_admin" => \Text::_("EXP_SETTINGS_SITE_LABEL_EDITOR_ADMIN"),
            "editor_site" => \Text::_("EXP_SETTINGS_SITE_LABEL_EDITOR_SITE"),
            "calendar" => \Text::_("EXP_SETTINGS_SITE_LABEL_CCALENDAR_SITE"),
            "captcha" => \Text::_("EXP_SETTINGS_SITE_LABEL_CAPTCHA_SITE"),
            "format_date" => \Text::_("EXP_SETTINGS_SITE_FORMATDATE"),
            "page_number" => \Text::_("EXP_SETTINGS_SITE_LABEL_PNUMBER"),
            "meta_robots" => \Text::_("EXP_SETTINGS_SITE_LABEL_ROBOTS"),
            "meta_desc" => \Text::_("EXP_SETTINGS_SITE_LABEL_TEGDESCRIPTION"),
            "meta_keys" => \Text::_("EXP_SETTINGS_SITE_LABEL_TEGKEYWORDS"),
            "meta_author" => \Text::_("EXP_SETTINGS_SITE_LABEL_TEGAUTOR"),
            "show_author" => \Text::_("EXP_SETTINGS_SITE_LABEL_SHOWAUTOR"),
            "error_reporting"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ERRORREPORT"),
            "path_log"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ERRORPATH"),
            "debug"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ENABDEBUG"),
            "viewstyle"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_VIEWSTYLE"),
            "viewposition"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_VIEWPOSITION"),
            "log_enable"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ENABLELOG"),
            "log_maxsize"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_MAXSIZELOG"),
            "time_cache"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_TIMECACHE"),
            "enable_cache"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ENABLECACHE"),
            "ses_name"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_SESID"),
            "ses_time"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_SESTIME"),
            "ses_path"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_SESPATH"),
            "timezone"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_TIMEZONE"),
            "charset"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_CHARSET"),
            "language"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_LOCAL"),
            "autolang"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_AUTOLANG"),
            "gzip"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_GZIP"),
            "fromname"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_FROM"),
            "mailfrom"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_MAILSITE"),
            "prefix"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_SUFFIX"),
            "enab_prefix"=> \Text::_("EXP_SETTINGS_SYSTEM_LABEL_ENABLESUFFIX"),
            "useFileTransport"=>\Text::_("EXP_SETTINGS_SYSTEM_USEFILETRANSPORT"),
            "enableCsrfValidation"=>\Text::_("EXP_SETTINGS_SYSTEM_CSRF"),
            "logWrite"=>\Text::_("EXP_SETTINGS_SYSTEM_LOGWRITE"),
            "type_cache"=>\Text::_("EXP_SETTINGS_SERVER_LABEL_CACHETYPE"),
            "memcache_host"=>\Text::_("EXP_SETTINGS_SERVER_LABEL_MEMCACHEHOST"),
            "memcache_port"=>\Text::_("EXP_SETTINGS_SERVER_LABEL_MEMCACHEPORT"),
            "memcache_username"=>\Text::_("EXP_SETTINGS_SERVER_LABEL_MEMCACHEUSERNAME"),
            "memcache_password"=>\Text::_("EXP_SETTINGS_SERVER_LABEL_MEMCACHEPASS")
        ];
    }

}
        