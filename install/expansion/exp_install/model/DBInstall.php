<?php

namespace exp\exp_install\model;

use maze\base\Model;
use RC;
use maze\db\Connection;

class DBInstall extends Model {

    public $host = 'localhost';
    public $user;
    public $password;
    public $dbname;
    public $prefix = 'maze';
    public $type = 'mysql';
    public $encoding = 'utf8';
    
    public $clear;


    public function rules() {
        return [
            [['host', 'user', 'password', 'dbname', 'prefix', 'type', 'encoding'], 'required', "message" => "Поле ({attribute}) является обязательным"],
            [['host', 'user', 'dbname'], 'match', 'pattern' => '/^[a-z-0-9_.]{2,}$/', "message" => "Поле ({attribute}) содержит недопустимые символы"],
            ['prefix', 'match', 'pattern' => '/^[a-z-0-9_]{3,}$/'],
            ['clear', 'boolean'],
            ['dbname', 'valudateConnect']
        ];
    }

    public function valudateConnect($attribute, $param) {
        if ($this->hasErrors()) {
            return false;
        }
        try {
            $db = new Connection([
                "dsn" => $this->type . ":host=" . $this->host . ";dbname=" . $this->dbname,
                "username" => $this->user,
                "password" => $this->password,
                "charset" => $this->encoding,
                "tablePrefix" => $this->prefix . "_",
                "queryCacheDuration" => (int) 15
            ]);
            $db->open();
            $innodb = $db->createCommand("SHOW VARIABLES LIKE 'innodb%'")->queryScalar();
            
            if (!$innodb) {
                $this->addError($attribute, "Ваш сервер MySql не поддерживает тип таблиц InnoDB");
            }

            $data = $db->createCommand("SHOW TABLES")->queryAll();
            if ($data && !$this->clear) {
                $this->addError($attribute, "База данных не пуста");               
            }else{
                $this->clear = true;
            }
        } catch (\Exception $ex) {
            $this->addError($attribute, "Ошибка подключения к базе данных " . $ex->getMessage());
        }
    }

    public function getIsEmptyDB() {
        if ($this->user && $this->type && $this->host && $this->dbname) {
            try {
                $db = new Connection([
                    "dsn" => $this->type . ":host=" . $this->host . ";dbname=" . $this->dbname,
                    "username" => $this->user,
                    "password" => $this->password,
                    "charset" => $this->encoding,
                    "tablePrefix" => $this->prefix . "_",
                    "queryCacheDuration" => (int) 15
                ]);
                $db->open();
                
                $data = $db->createCommand("SHOW TABLES")->queryAll();
                if($data){
                    return false;
                }
                
            } catch (\Exception $ex) {
                
            }
        }
        return true;
    }

    public function attributeLabels() {
        return[
            "host" => "Сервер",
            "user" => "Пользователь",
            "password" => "Пароль",
            "dbname" => "Имя базы данных",
            "prefix" => "Префикс",
            "type" => "Тип базы данных",
            "encoding" => "Кодировка",
            "clear"=>"Отчистить базу данных"
        ];
    }

}
