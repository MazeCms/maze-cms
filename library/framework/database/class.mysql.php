<?php

defined('_CHECK_') or die("Access denied");

/////////////////////////////////
// работа с базой данных 
/////////////////////////////////

class Mysql {

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private $_db_name;      // дескриптор текущей подлюченной базы данных, класса Mysql_connect
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $_all_query = array();    // "копилка" запросов
    private $_prefix;
    private $_query;

    public function __construct($db_name, $prefix) {

        $this->_db_name = $db_name;
        $this->_prefix = $prefix;
    }

    private function setBDPrefix($query) {
        return preg_replace("#(PREF[_])+#i", $this->_prefix, $query);
    }

    public function __get($index) {
        if ($index == "_query") {
            return $this->_query;
        }
    }

    public function getQuery() {
        return self::$_all_query;
    }

    //////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде дескриптора результирующией таблицы
    //////////////////////////////////////////////////////

    public function query($query = null) {
        $query = $query == null ? $this->_query : $query;

        if (empty($query))
            return null;

        $query = $this->setBDPrefix($query);

        $result = mysql_query($query, $this->_db_name);

        $this->CLEAR(); // чистим буффер запросов

        if (!$result) {
            Error::setDBO(mysql_error() . "<br>" . $query);
        }

        array_push(self::$_all_query, $query); // копилка запросов
        return $result;
    }

    public function CLEAR() {
        $this->_query = "";

        return $this;
    }

    public function SELECT($text) {
        $this->_query = "SELECT " . $text;
        return $this;
    }

    public function DEL($text = "") {
        $this->_query = "DELETE " . $text;
        return $this;
    }

    public function FROM($text) {
        $this->_query .= " FROM " . $text;
        return $this;
    }

    public function JOIN($text) {
        $this->_query .= " JOIN " . $text;
        return $this;
    }

    public function LJOIN($text) {
        $this->_query .= " LEFT JOIN " . $text;
        return $this;
    }

    public function RJOIN($text) {
        $this->_query .= " RIGHT JOIN " . $text;
        return $this;
    }

    public function ON($text) {
        $this->_query .= " ON " . $text;
        return $this;
    }

    public function WHERE($text = "") {
        $this->_query .= " WHERE " . $text;
        return $this;
    }

    public function JIN($column, $text) {
        if (is_array($text)) {
            $result = array();
            foreach ($text as $val) {
                if (is_string($val)) {
                    $result[] = "'" . $val . "'";
                } else {
                    $result[] = $val;
                }
            }
            $text = $result;

            $this->_query .= $column . " IN (" . implode(',', $text) . ") ";
        } else {
            $text = is_string($text) ? "'" . $text . "'" : $text;
            $this->_query .= $column . " IN (" . $text . ") ";
        }

        return $this;
    }

    public function NOTIN($column, $text) {
        if (is_array($text)) {
            $result = array();
            foreach ($text as $val) {
                if (is_string($val)) {
                    $result[] = "'" . $val . "'";
                } else {
                    $result[] = $val;
                }
            }
            $text = $result;

            $this->_query .= $column . " NOT IN (" . implode(',', $text) . ") ";
        } else {
            $text = is_string($text) ? "'" . $text . "'" : $text;
            $this->_query .= $column . " NOT IN (" . $text . ") ";
        }

        return $this;
    }

    public function JAND($text = "") {
        if (is_array($text)) {
            $this->_query .= implode(" AND ", $text);
        } else {
            $this->_query .= " AND " . $text;
        }

        return $this;
    }

    public function JOR($text = "") {
        if (is_array($text)) {
            $this->_query .= implode(" OR ", $text);
        } else {
            $this->_query .= " OR " . $text;
        }

        return $this;
    }

    public function NOT($text = "") {
        $this->_query .= " NOT " . $text;
        return $this;
    }

    public function ORDER($text, $asc = null) {
        $this->_query .= " ORDER BY " . $text;
        if ($asc !== null)
            $this->_query .= " " . $asc;
        return $this;
    }

    public function LIMIT($from, $to = null) {
        $this->_query .= " LIMIT " . $from;
        if ($to !== null)
            $this->_query .= ", " . $to;
        return $this;
    }

    public function UPDATE_T($table) {
        $this->_query .=" UPDATE $table ";
        return $this;
    }

    public function SET($object) {
        $sets = array();

        foreach ($object as $key => $value) {
            $key = mysql_real_escape_string($key . '');

            if ($value === null) {
                $sets[] = "$key=NULL";
            } else {
                $value = mysql_real_escape_string($value . '');
                $sets[] = "$key='$value'";
            }
        }

        $sets_s = implode(',', $sets);

        $this->_query .=" SET $sets_s ";
        return $this;
    }

    public function AFFROWS() {
        return mysql_affected_rows($this->_db_name);
    }

    public function textSQL($text) {
        $this->_query .= $text;
        return $this;
    }

    public function GROUP($text) {
        $this->_query .= " GROUP BY " . $text;
        return $this;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде многомерного индексно - ассоциативного массива
    // вида $arr[0 - строка текуцей выборки]["ключ - имя поля"] 
    ///////////////////////////////////////////////////////////

    public function result_assoc_arr($query = null) {
        $result = $this->query($query);


        $arr_res = array();
        while ($arr = mysql_fetch_assoc($result)) {
            $arr_res[] = $arr;
        }
        return $arr_res;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде массива объектов класса stdClass
    // вида $arr[0 - строка текуцей выборки]->имя поля
    ///////////////////////////////////////////////////////////

    public function result_arr_obj($query = null) {
        $result = $this->query($query);



        $arr_res = array();
        while ($arr = mysql_fetch_object($result)) {
            $arr_res[] = $arr;
        }
        return $arr_res;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде  - индексного массива
    // вида $arr[0].... 
    ///////////////////////////////////////////////////////////

    public function result_row($query = null) {
        $result = $this->query($query);
        $arr = mysql_fetch_row($result);

        return $arr;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде  - индексного массива
    // вида $arr[0][0].... 
    ///////////////////////////////////////////////////////////

    public function result_row_arr($query = null) {
        $result = $this->query($query);

        $arr_res = array();
        while ($arr = mysql_fetch_row($result)) {
            $arr_res[] = $arr;
        }
        return $arr_res;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает результат
    // ввиде  - ассоциативного массива
    // вида $arr["ключ - имя поля"] 
    ///////////////////////////////////////////////////////////

    public function result_assoc($query = null) {
        $result = $this->query($query);

        $arr = mysql_fetch_assoc($result);

        return $arr;
    }

    ////////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает один результат
    // ввиде объекта класса stdClass
    // вида $res->имя поля
    ///////////////////////////////////////////////////////////

    public function result_object($query = null) {
        $result = $this->query($query);

        $arr = mysql_fetch_object($result);

        return $arr;
    }

    ///////////////////////////////////////////////////////////
    // Выполняет SQL запрос $query и возвращает  регультат
    // ввиде 0 ячейки
    ///////////////////////////////////////////////////////////

    public function result($query = null) {
        $result = $this->query($query);

        $arr = mysql_result($result, 0);

        return $arr;
    }

    //
    // Вставка строки
    // $table 		- имя таблицы
    // $object 		- ассоциативный массив с парами вида "имя столбца - значение"
    // результат	- идентификатор новой строки
    //
	public function insert($table, $object) {
        if ($this->is_Numeric($object))
            return $this->insertList($table, $object);

        $columns = array();
        $values = array();

        foreach ($object as $key => $value) {
            $key = mysql_real_escape_string($key . '');
            $columns[] = $key;

            if ($value === null) {
                $values[] = 'NULL';
            } else {
                $value = mysql_real_escape_string($value . '');
                $values[] = "'$value'";
            }
        }

        $columns_s = implode(',', $columns);
        $values_s = implode(',', $values);

        $query = "INSERT INTO $table ($columns_s) VALUES ($values_s)";

        $result = $this->query($query);

        return mysql_insert_id($this->_db_name);
    }

    public function insertList($table, $object) {

        $result = array();
        $columns = "";

        foreach ($object as $value) {
            $values = array();

            foreach ($value as $val) {
                $val = mysql_real_escape_string($val . '');
                $values[] = "'$val'";
            }

            $result[] = "(" . implode(',', $values) . ")";
        }
        if ($this->is_Assoc($object[0])) {
            foreach ($object[0] as $key => $value)
                $columns[] = mysql_real_escape_string($key . '');

            $columns = "(" . implode(',', $columns) . ")";
        }
        $result_s = implode(',', $result);

        $query = "INSERT INTO $table $columns VALUES $result_s";

        $this->query($query);

        return mysql_insert_id($this->_db_name);
    }

    public function replace($table, $object) {

        $result = array();
        $columns = "";

        foreach ($object as $value) {
            $values = array();
            if (is_array($value)) {
                foreach ($value as $val) {
                    $val = mysql_real_escape_string($val . '');
                    $values[] = "'$val'";
                }
                $result[] = "(" . implode(',', $values) . ")";
            } else {
                $result[] = mysql_real_escape_string($value . '');
            }
        }


        if (isset($object[0]) && $this->is_Assoc($object[0])) {
            foreach ($object[0] as $key => $value)
                $columns[] = mysql_real_escape_string($key . '');

            $columns = "(" . implode(',', $columns) . ")";
        }
        if ($this->is_Assoc($object)) {
            $result_s = "(" . implode(',', $result) . ")";
            foreach ($object as $key => $value)
                $columns[] = mysql_real_escape_string($key . '');

            $columns = "(" . implode(',', $columns) . ")";
        } else {
            $result_s = implode(',', $result);
        }


        $query = "REPLACE INTO $table $columns VALUES $result_s";

        $this->query($query);

        return mysql_insert_id($this->_db_name);
    }

    public function is_Assoc($arr) {
        return (is_array($arr) && count(array_filter(array_keys($arr), 'is_string')) == count($arr));
    }

    public function is_Numeric($arr) {
        return (is_array($arr) && count(array_filter(array_keys($arr), 'is_numeric')) == count($arr));
    }

    //
    // Изменение строк
    // $table 		- имя таблицы
    // $object 		- ассоциативный массив с парами вида "имя столбца - значение"
    // $where		- условие (часть SQL запроса)
    // результат	- число измененных строк
    //	
    public function update($table, $object, $where = false) {
        $sets = array();

        foreach ($object as $key => $value) {
            $key = mysql_real_escape_string($key . '');

            if ($value === null) {
                $sets[] = "$key=NULL";
            } else {
                $value = mysql_real_escape_string($value . '');
                $sets[] = "$key='$value'";
            }
        }

        $sets_s = implode(',', $sets);
        $where = $where ? "WHERE " . $where : "";
        $query = "UPDATE $table SET $sets_s  $where";
        $result = $this->query($query);
        return mysql_affected_rows($this->_db_name);
    }

    /*
      /////////////////////////////////////////////////////////////
      // Удаление строк
      // $table 		- имя таблицы
      // $where		- условие (часть SQL запроса)
      // результат	- число удаленных строк
      /////////////////////////////////////////////////////////////
     */

    public function delete($table, $where) {
        $query = "DELETE FROM $table WHERE $where";
        $result = $this->query($query);
        return mysql_affected_rows($this->_db_name);
    }

    /*
      /////////////////////////////////////////////////////////
      // Следующий максимальный Auto Increment
      ////////////////////////////////////////////////////////
     */

    public function getAutoIncrement($table) {
        $query = "SHOW TABLE STATUS LIKE '$table' ";
        $increment = $this->result_assoc($query);
        return $increment['Auto_increment'];
    }

}
