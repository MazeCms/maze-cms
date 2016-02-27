<?php

defined('_CHECK_') or die("Access denied");
use maze\helpers\ArrayHelper;

class URI {

    protected $uri = null;
    protected $scheme = null;
    protected $host = null;
    protected $port = null;
    protected $user = null;
    protected $pass = null;
    protected $path = null;
    protected $query = null;
    protected $fragment = null;
    protected $vars = array();
    protected static $instances = array();
    protected static $base = array();
    protected static $root = array();
    protected static $current;

    public function __construct($uri = null) {
        if (!is_null($uri) && is_array($uri)) {
            if(isset($uri[0]) && is_string($uri[0]))
            {
                $this->parse(RC::getAlias($uri[0]));
            }
            if(isset($uri[0]) && ArrayHelper::isAssociative($uri[0]))
            {
                $this->parse(static::instance()->toString(['path', 'query', 'fragment']));
                $this->setQuery($uri[0]);
            }
            if(isset($uri[1]))
            {
                $this->setQuery($uri[1]);
            }
            
        } elseif (!is_null($uri)) {
            $this->parse($uri);
        }
    }

    public function __toString() {
        return $this->toString();
    }

    public static function instance($uri = 'SERVER') {
        if (empty(self::$instances[$uri])) {
            if ($uri == 'SERVER') {
                if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
                    $https = 's://';
                } else {
                    $https = '://';
                }


                if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI'])) {
                    $theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                } else {

                    $theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

                    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                        $theURI .= '?' . $_SERVER['QUERY_STRING'];
                    }
                }
            } else {
                $theURI = $uri;
            }

            self::$instances[$uri] = new self($theURI);
        }
        return self::$instances[$uri];
    }

    public static function base($pathonly = false) {

        if (empty(self::$base)) {
            $uri = self::instance();
            self::$base['prefix'] = $uri->toString(array('scheme', 'host', 'port'));

            if (strpos(php_sapi_name(), 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
                $script_name = $_SERVER['PHP_SELF'];
            } else {
                $script_name = $_SERVER['SCRIPT_NAME'];
            }

            self::$base['path'] = rtrim(dirname($script_name), '/\\');
        }

        return $pathonly === false ? self::$base['prefix'] . self::$base['path'] . '/' : self::$base['path'];
    }

    public static function root($pathonly = false, $path = null) {
        if (empty(self::$root)) {
            $uri = self::instance(self::base());
            self::$root['prefix'] = $uri->toString(array('scheme', 'host', 'port'));
            self::$root['path'] = rtrim($uri->toString(array('path')), '/\\');
        }

        if (isset($path)) {
            self::$root['path'] = $path;
        }

        return $pathonly === false ? self::$root['prefix'] . self::$root['path'] . '/' : self::$root['path'];
    }

    public static function current() {
        if (empty(self::$current)) {
            $uri = self::instance();
            self::$current = $uri->toString(array('scheme', 'host', 'port', 'path', 'query'));
        }

        return self::$current;
    }

    public static function reset() {
        self::$instances = array();
        self::$base = array();
        self::$root = array();
        self::$current = '';
    }

    public function parse($uri) {
        $this->uri = $uri;

        $parts = parse_url($uri);

        $retval = ($parts) ? true : false;

        if (isset($parts['query']) && strpos($parts['query'], '&amp;')) {
            $parts['query'] = str_replace('&amp;', '&', $parts['query']);
        }

        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : null;
        $this->user = isset($parts['user']) ? $parts['user'] : null;
        $this->pass = isset($parts['pass']) ? $parts['pass'] : null;
        $this->host = isset($parts['host']) ? $parts['host'] : null;
        $this->port = isset($parts['port']) ? $parts['port'] : null;
        $this->path = isset($parts['path']) ? $parts['path'] : null;
        $this->query = isset($parts['query']) ? $parts['query'] : null;
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : null;


        if (isset($parts['query'])) {
            parse_str($parts['query'], $this->vars);
        }

        return $retval;
    }

    public function toString(array $parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment')) {
        $query = $this->getQuery();

        $uri = '';
        $uri .= in_array('scheme', $parts) ? (!empty($this->scheme) ? $this->scheme . '://' : '') : '';
        $uri .= in_array('user', $parts) ? $this->user : '';
        $uri .= in_array('pass', $parts) ? (!empty($this->pass) ? ':' : '') . $this->pass . (!empty($this->user) ? '@' : '') : '';
        $uri .= in_array('host', $parts) ? $this->host : '';
        $uri .= in_array('port', $parts) ? (!empty($this->port) ? ':' : '') . $this->port : '';
        $uri .= in_array('path', $parts) ? $this->path : '';
        $uri .= in_array('query', $parts) ? (!empty($query) ? '?' . $query : '') : '';
        $uri .= in_array('fragment', $parts) ? (!empty($this->fragment) ? '#' . $this->fragment : '') : '';

        return $uri;
    }

    public function setVar($name, $value) {
        $tmp = isset($this->vars[$name]) ? $this->vars[$name] : null;

        $this->vars[$name] = $value;

        $this->query = null;

        return $tmp;
    }

    public function hasVar($name) {
        return array_key_exists($name, $this->vars);
    }

    public function hasVars() {
        return !empty($this->vars);
    }

    public function getVar($name, $default = null) {
        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }
        return $default;
    }

    public function delVar($name) {
        if (array_key_exists($name, $this->vars)) {
            unset($this->vars[$name]);

            $this->query = null;
        }
    }

    public function setQuery($query) {
        if (is_array($query)) {
            $this->vars = $query;
        } else {
            if (strpos($query, '&amp;') !== false) {
                $query = str_replace('&amp;', '&', $query);
            }
            parse_str($query, $this->vars);
        }

        $this->query = null;
    }
    
    public function mergeQuery($query)
    {
        $varOld = $this->getQuery(true);
        $this->setQuery($query);
        $varNew = $this->getQuery(true);
        if(!$varOld) $varOld = [];
        if(!$varNew) $varNew = [];
        $this->setQuery(array_merge($varOld, $varNew));
    }

    public function getQuery($toArray = false) {
        if ($toArray) {
            return $this->vars;
        }

        if (is_null($this->query)) {
            $this->query = self::buildQuery($this->vars);
        }

        return $this->query;
    }

    public static function buildQuery(array $params) {
        if (count($params) == 0) {
            return false;
        }

        return http_build_query($params, '', '&');
    }

    public function getScheme() {
        return $this->scheme;
    }

    public function setScheme($scheme) {
        $this->scheme = $scheme;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getPass() {
        return $this->pass;
    }

    public function setPass($pass) {
        $this->pass = $pass;
    }

    public function getHost() {
        return $this->host;
    }

    public function setHost($host) {
        $this->host = $host;
    }

    public function getPort() {
        return (isset($this->port)) ? $this->port : null;
    }

    public function setPort($port) {
        $this->port = $port;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $this->_cleanPath($path);
    }

    public function getFragment() {
        return $this->fragment;
    }

    public function setFragment($anchor) {
        $this->fragment = $anchor;
    }

    public function isSSL() {
        return $this->getScheme() == 'https' ? true : false;
    }

    public static function isInternal($url) {
        $uri = self::instance($url);
        $base = $uri->toString(array('scheme', 'host', 'port', 'path'));
        $host = $uri->toString(array('scheme', 'host', 'port'));
        if (stripos($base, self::base()) !== 0 && !empty($host)) {
            return false;
        }
        return true;
    }

    protected function _cleanPath($path) {
        $path = explode('/', preg_replace('#(/+)#', '/', $path));

        for ($i = 0, $n = count($path); $i < $n; $i++) {
            if ($path[$i] == '.' || $path[$i] == '..') {
                if (($path[$i] == '.') || ($path[$i] == '..' && $i == 1 && $path[0] == '')) {
                    unset($path[$i]);
                    $path = array_values($path);
                    $i--;
                    $n--;
                } elseif ($path[$i] == '..' && ($i > 1 || ($i == 1 && $path[0] != ''))) {
                    unset($path[$i]);
                    unset($path[$i - 1]);
                    $path = array_values($path);
                    $i -= 2;
                    $n -= 2;
                }
            }
        }

        return implode('/', $path);
    }

}
