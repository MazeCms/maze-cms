<?php

namespace exp\exp_settings\form;

use maze\base\Model;
use maze\helpers\ArrayHelper;
use maze\db\Connection;
use RC;

class FormDB extends Model {

    /**
     * @var string - индитификатор подключения к БД, основное default
     */
    public $key;
    
    /**
     * @var string - кодировка БД utf8
     */
    public $encoding;
    
    /**
     * @var string - тип базы банных (драйвер) mysql
     */
    public $type;
    
    /**
     * @var string - название или IP хоста БД
     */
    public $host;
    
    /**
     * @var string - имя базы данных
     */
    public $bdname;
    
    /**
     * @var string - префикс таблиц
     */
    public $dbprefix;
    
    /**
     * @var string - имя пользователя БД
     */
    public $user;
    
    /**
     * @var string - пароль БД
     */
    public $password;
    
    /**
     * @var boolean - активировать подвключение к БД
     */
    public $connect;
    
    
    public function rules() {
        return [
            [['key', 'encoding', 'type', 'host', 'bdname', 'dbprefix', 'user'], "required"],
            ['connect', 'boolean'],
            ['key', 'match', 'pattern'=>'/^[a-z]{4,15}$/'],
            ['dbprefix', 'match', 'pattern'=>'/^[a-z-0-9_]{4,15}$/i'],
            ['type', 'in', 'range'=>['mysql', 'pgsql', 'sqlite', 'sqlsrv', 'cubrid', 'oci']],
            [['password', 'encoding'], 'string'],
            ['key', 'validConnect']
        ];
    }
   
    public function validConnect($attribute, $params){
        if($this->hasErrors()) return false;
        try{
             $connect = new Connection([
                "dsn" => $this->type . ":host=" . $this->host . ";dbname=" . $this->bdname,
                "username" => $this->user,
                "password" => $this->password,
                "charset" => $this->encoding,
                "tablePrefix" => $this->dbprefix . "_"
            ]);
            
            $connect->open();
        }
        catch (\Exception $e) {
           $this->addError('key', \Text::_('Ошибка подключения к базе данных')); 
        } 
        
       
    }
    public function attributeLabels() {
        return[
            "key" => \Text::_("EXP_SETTINGS_SERVER_LABEL_ID"),
            "encoding" => \Text::_("EXP_SETTINGS_SERVER_LABEL_ENCODING"),
            "type" => \Text::_("EXP_SETTINGS_SERVER_LABEL_TYPE"),
            "host" => \Text::_("EXP_SETTINGS_SERVER_LABEL_HOST"),
            "bdname" => \Text::_("EXP_SETTINGS_SERVER_LABEL_NAMEBD"),
            "dbprefix" => \Text::_("EXP_SETTINGS_SERVER_LABEL_PREF"),
            "user" => \Text::_("EXP_SETTINGS_SERVER_LABEL_USER"),
            "connect" => \Text::_("EXP_SETTINGS_SERVER_LABEL_CONECT"),
            "password" => \Text::_("EXP_SETTINGS_SERVER_LABEL_PASS")           
        ];
    }

}
        