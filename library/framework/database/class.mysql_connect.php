<?php

defined('_CHECK_') or die("Access denied");
/*
  //////////////////////////////////
  // Подключаемся к базе данных
  //////////////////////////////////
 */

class Mysql_connect {

    ///////////////////////////////////////////////////////////////
    private static $instance; // экземпляр класса MySQL_conect
    ///////////////////////////////////////////////////////////////
    public $_dbcnx = array(); // массив дескриптора соединения с БД
    ////////////////////////////////////////////////////////////////
    protected $options; // массив параметров соединения в БД

    /*
      ///////////////////////////////////////////////
      // Получение экземпляра класса
      // результат	- экземпляр класса MySQL_conect
      /////////////////////////////////////////////////
     */

    public static function Instance($options) {
        if (self::$instance == null)
            self::$instance = new self($options);

        return self::$instance;
    }

    /*
      ////////////////////////////////////////////
      // Устанавливаем соединениие с базой данных
      ////////////////////////////////////////////
     */

    private function __construct($options) {

        $this->options = $options;

        foreach ($this->options as $index => $val) {
            if (($val["type"] !== "mysql") || !$val["connect"])
                continue;

            $this->connect_db($index, $val);
        }
    }

    private function connect_db($name, $param) {
        $connect = @mysql_connect($param["host"], $param["user"], $param["password"], true);

        if (!$connect)
            Error::setDBO("Не удается подключиться к серверу баз данных", 500);

        if (!@mysql_select_db($param["bdname"], $connect))
            Error::setDBO("Не удается подключиться к базе данных сервера", 500);

        @mysql_query("SET NAMES '" . $param["encoding"] . "'", $connect);

        $this->_dbcnx[$name] = $connect;
    }

}
