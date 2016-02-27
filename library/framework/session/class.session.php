<?php

defined('_CHECK_') or die("Access denied");

use maze\base\Object;

class Session extends Object {

    /**
     * @var string $sesPath - путь где хранятся файлы сессии
     */
    public $sesPath = "@root/temp/session";
    
    /**
     * @var int $sesTime - время жизни сесси в секундах
     */
    public $sesTime = 1440;
    
    /**
     * @var int $sesName - ID  сессии
     */
    public $sesName = "SID";
    
    /**
     * @var boolean $sesSsl - использоваеть защищенный режим
     */
    public $sesSsl = false;
    
    public function init() {
        if (session_id()) {
            session_unset();
            session_destroy();
        }

        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_cookies', '1');
        // PHP использует только cookis для передачи SID
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        if ($this->sesPath && is_dir(RC::getAlias($this->sesPath))) {
            $path = RC::getAlias($this->sesPath);
        } else {
            $path = PATH_ROOT . DS . "temp" . DS . "session";
        }
        
        session_save_path($path);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 50);

        $this->setSessionParams();

        $this->setCookieParams();
    }


    public function sessoinStart() {
        register_shutdown_function('session_write_close');
        session_cache_limiter('nocache');

        session_start();

        $this->setTimers();
        $this->setCounter();
        return true;
    }

    public function getAll() {
        return $_SESSION;
    }

    public function get($name, $namespace = 'default') {
        if (isset($_SESSION[$namespace][$name])) {
            return $_SESSION[$namespace][$name];
        }
        return false;
    }

    public function set($name, $value = null, $namespace = 'default') {
        $old = isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : null;

        if (null === $value) {
            unset($_SESSION[$namespace][$name]);
        } else {
            $_SESSION[$namespace][$name] = $value;
        }

        return $old;
    }

    public function isSess($name, $namespace = 'default') {
        return isset($_SESSION[$namespace][$name]);
    }

    public function clear($name, $namespace = 'default') {
        $value = null;
        if ($name == $namespace) {
            unset($_SESSION[$namespace]);
        }
        if (isset($_SESSION[$namespace][$name])) {
            $value = $_SESSION[$namespace][$name];
            unset($_SESSION[$namespace][$name]);
        }

        return $value;
    }

    public function destroy() {
        RC::app()->document->getCookies()->remove(session_name());       
        session_unset();
        session_destroy();
        return true;
    }

    public function close() {
        session_write_close();
    }

    public function getSessionId() {
        return session_id();
    }

    protected function setCookieParams() {
        $cookie = session_get_cookie_params();

        if ($this->sesSsl) {
            $cookie['secure'] = true;
        }

        session_set_cookie_params($cookie['lifetime'], $cookie['path'], $cookie['domain'], $cookie['secure'], true);
        return true;
    }

    protected function setSessionParams() {

        $this->sesName = $this->sesName ? $this->sesName : "SID";

        $this->sesTime = $this->sesTime ? $this->sesTime : 1440; 
     

        session_name($this->sesName);
        // время после которого данные уничтожаются
        ini_set('session.gc_maxlifetime', $this->sesTime);

        return true;
    }

    protected function setTimers() {
        if (!$this->isSess('timer_start')) {
            $start = time();

            $this->set('timer_start', $start);   // время начало сессии
            $this->set('timer_last', $start);  // время последнего обновления страницы
            $this->set('timer_now', $start);   // текущее время
        }

        $this->set('timer_last', $this->get('timer_now'));
        $this->set('timer_now', time());

        return true;
    }

    protected function setCounter() {

        $counter = $this->get('counter');
        if (!$counter) {
            $this->set('counter', 1);
            return true;
        }
        $counter++;

        $this->set('counter', $counter);
        return true;
    }

    /* 	
      /////////////////////////////////////////////////////////////////////////////////////////
      // 								ГЕНЕРАТОР СЛУЧАЙНЫХ КЛЮЧЕЙ
      /////////////////////////////////////////////////////////////////////////////////////////
      // @param (int) - длина кода сгенерированная в случайной последовательности букв и цифр
      // return (string) - возвращает сгенерированый код
      /////////////////////////////////////////////////////////////////////////////////////////
     */

    public function generateKey($number) {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'q', 'p', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F',
            'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'Q', 'P', 'R', 'S',
            'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0', '_');

        $pass = "";
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    public function generateCode($number) {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'q', 'p', 'r', 's',
            't', 'u', 'v', 'w', 'x', 'y', 'z');

        $pass = "";
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }
    
    public function generateNumKey($number) {
        $arr = array(
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');

        $pass = "";
        for ($i = 0; $i < $number; $i++) {
            $index = rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }

}

?>