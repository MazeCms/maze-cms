<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\StringHelper;
use maze\base\HeaderCollection;
use maze\base\Cookie;
use maze\base\CookieCollection;

class Request {

    /**
     * The name of the HTTP header for sending CSRF token.
     */
    const CSRF_HEADER = 'X-CSRF-Token';

    /**
     * The length of the CSRF token mask.
     */
    const CSRF_MASK_LENGTH = 8;

    protected static $_instance;
    protected $var = array();
    protected $_csrfToken;
    protected $_port;
    protected $_headers;
    protected $_languages;
    protected $_contentTypes;
    protected $_securePort;
    protected $_cookies;
    public $csrfParam = 'csrf';
    public $enableCsrfValidation = true;

    public function __construct() {
        
    }

    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod() {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
    }

    /**
     * Returns whether this is an OPTIONS request.
     * @return boolean whether this is a OPTIONS request.
     */
    public function isOptions() {
        return $this->getMethod() === 'OPTIONS';
    }

    /**
     * Returns whether this is a HEAD request.
     * @return boolean whether this is a HEAD request.
     */
    public function isHead() {
        return $this->getMethod() === 'HEAD';
    }

    public function isGet() {
        return $this->getMethod() == 'GET';
    }

    public function isPost() {
        return $this->getMethod() == 'POST';
    }

    /**
     * Returns whether this is a DELETE request.
     * @return boolean whether this is a DELETE request.
     */
    public function isDelete() {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * Returns whether this is a PUT request.
     * @return boolean whether this is a PUT request.
     */
    public function isPut() {
        return $this->getMethod() === 'PUT';
    }

    /**
     * Returns whether this is a PATCH request.
     * @return boolean whether this is a PATCH request.
     */
    public function getIsPatch() {
        return $this->getMethod() === 'PATCH';
    }

    /**
     * Returns whether this is an Adobe Flash or Flex request.
     * @return boolean whether this is an Adobe Flash or Adobe Flex request.
     */
    public function isFlash() {
        return isset($_SERVER['HTTP_USER_AGENT']) &&
                (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== false);
    }

    public function isAjax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    public function isJson() {
        $accept = $this->getHeaders()->get("accept");
        return substr_count($accept, "application/json") > 0 ? true : false;
    }

    public function isJS() {
        $accept = $this->getHeaders()->get("accept");
        return substr_count($accept, "application/javascript") > 0 ? true : false;
    }

    public function isText() {
        $accept = $this->getHeaders()->get("accept");
        return substr_count($accept, "text/plain") > 0 ? true : false;
    }

    public function isHtml() {
        $accept = $this->getHeaders()->get("accept");
        return substr_count($accept, "text/html") > 0 ? true : false;
    }

    public function isCss() {
        $accept = $this->getHeaders()->get("accept");
        return substr_count($accept, "text/css") > 0 ? true : false;
    }

    public function isPjax() {
        return $this->isAjax() && !empty($_SERVER['HTTP_X_PJAX']);
    }

    /**
     * Return if the request is sent via secure channel (https).
     * @return boolean if the request is sent via secure channel (https)
     */
    public function getIsSecureConnection() {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    /**
     * Returns the port to use for insecure requests.
     * Defaults to 80, or the port specified by the server if the current
     * request is insecure.
     * @return integer port number for insecure requests.
     * @see setPort()
     */
    public function getPort() {
        if ($this->_port === null) {
            $this->_port = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * Returns the port to use for secure requests.
     * Defaults to 443, or the port specified by the server if the current
     * request is secure.
     * @return integer port number for secure requests.
     * @see setSecurePort()
     */
    public function getSecurePort() {
        if ($this->_securePort === null) {
            $this->_securePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * Returns the user agent, null if not present.
     * @return string user agent, null if not present
     */
    public function getUserAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Returns the user IP address.
     * @return string user IP address. Null is returned if the user IP address cannot be detected.
     */
    public function getUserIP() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost() {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    /**
     * @return string the username sent via HTTP authentication, null if the username is not given
     */
    public function getAuthUser() {
        return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
    }

    /**
     * @return string the password sent via HTTP authentication, null if the password is not given
     */
    public function getAuthPassword() {
        return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
    }

    public function getReferrer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    public function get($var = null, $filter = ["decode", "deleteteg", "html"], $default = null) {
        if ($var === null) {
            $result = static::getMetod('GET', $filter);
        } else {
            $result = static::getVar($var, "GET", $filter);
        }

        return $result === false && $default !== null ? $default : $result;
    }

    public function hasGet($var) {
        return static::hasVar($var, "GET");
    }

    public function post($var = null, $filter = ["decode", "deleteteg", "html"], $default = null) {
        if ($var === null) {
            $result = static::getMetod('POST', $filter);
        } else {
            $result = static::getVar($var, "POST", $filter);
        }

        return $result === false && $default !== null ? $default : $result;
    }

    public function getPost($var = null, $filter = ["decode", "deleteteg", "html"]) {
        if ($var === null) {
            return static::getMetod('POST', $filter);
        }

        return static::getVar($var, "POST", $filter);
    }

    public function hasPost($var) {
        return static::hasVar($var, "POST");
    }

    public function getAll($var = null, $filter = ["decode", "deleteteg", "html"]) {
        if ($var === null) {
            return static::getMetod('REQ', $filter);
        }

        return static::getVar($var, "REQ", $filter);
    }

    public function hasAll($var) {
        return static::hasVar($var, "REQ");
    }

    public  function getProtocol() {
        if ($this->getIsSecureConnection()) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }
        return $protocol;
    }

    public function getDomain() {
        return $_SERVER['HTTP_HOST'];
    }

    public function siteUrl() {
        return $this->getProtocol() . $this->getDomain();
    }

    public function getBaseUrl() {
        return $this->getProtocol() . $this->getDomain();
    }

    public function getCsrfToken($regenerate = false) {
        if ($this->_csrfToken === null || $regenerate) {
            if ($regenerate || ($token = $this->loadCsrfToken()) === false) {
                $token = $this->generateCsrfToken();
            }
            // the mask doesn't need to be very random
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-.';
            $mask = substr(str_shuffle(str_repeat($chars, 5)), 0, self::CSRF_MASK_LENGTH);
            // The + sign may be decoded as blank space later, which will fail the validation
            $this->_csrfToken = str_replace('+', '.', base64_encode($mask . $this->xorTokens($token, $mask)));
        }

        return $this->_csrfToken;
    }

    /**
     * Loads the CSRF token from cookie or session.
     * @return string the CSRF token loaded from cookie or session. Null is returned if the cookie or session
     * does not have CSRF token.
     */
    protected function loadCsrfToken() {
        return RC::app()->session->get($this->csrfParam);
    }

    /**
     * Generates  an unmasked random token used to perform CSRF validation.
     * @return string the random token for CSRF validation.
     */
    protected function generateCsrfToken() {
        $token = RC::app()->session->generateKey(50);

        RC::app()->session->set($this->csrfParam, $token);
        return $token;
    }

    /**
     * Returns the XOR result of two strings.
     * If the two strings are of different lengths, the shorter one will be padded to the length of the longer one.
     * @param string $token1
     * @param string $token2
     * @return string the XOR result
     */
    private function xorTokens($token1, $token2) {
        $n1 = StringHelper::byteLength($token1);
        $n2 = StringHelper::byteLength($token2);
        if ($n1 > $n2) {
            $token2 = str_pad($token2, $n1, $token2);
        } elseif ($n1 < $n2) {
            $token1 = str_pad($token1, $n2, $n1 === 0 ? ' ' : $token1);
        }

        return $token1 ^ $token2;
    }

    /**
     * @return string the CSRF token sent via [[CSRF_HEADER]] by browser. Null is returned if no such header is sent.
     */
    public function getCsrfTokenFromHeader() {
        $key = 'HTTP_' . str_replace('-', '_', strtoupper(self::CSRF_HEADER));
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    /**
     * Performs the CSRF validation.
     * The method will compare the CSRF token obtained from a cookie and from a POST field.
     * If they are different, a CSRF attack is detected and a 400 HTTP exception will be raised.
     * This method is called in [[Controller::beforeAction()]].
     * @return boolean whether CSRF token is valid. If [[enableCsrfValidation]] is false, this method will return true.
     */
    public function validateCsrfToken() {
        $method = $this->getMethod();
        // only validate CSRF token on non-"safe" methods http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.1.1
        if (!$this->enableCsrfValidation || in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $trueToken = $this->loadCsrfToken();

        return $this->validateCsrfTokenInternal($this->post($this->csrfParam), $trueToken) || $this->validateCsrfTokenInternal($this->getCsrfTokenFromHeader(), $trueToken);
    }

    /**
     * Validates CSRF token
     *
     * @param string $token
     * @param string $trueToken
     * @return boolean
     */
    private function validateCsrfTokenInternal($token, $trueToken) {
        $token = base64_decode(str_replace('.', '+', $token));
        $n = StringHelper::byteLength($token);
        if ($n <= self::CSRF_MASK_LENGTH) {
            return false;
        }
        $mask = StringHelper::byteSubstr($token, 0, self::CSRF_MASK_LENGTH);
        $token = StringHelper::byteSubstr($token, self::CSRF_MASK_LENGTH, $n - self::CSRF_MASK_LENGTH);
        $token = $this->xorTokens($mask, $token);


        return $token === $trueToken;
    }

    public static function hasVar($name, $metod = "GET") {
        if ($metod == "GET") {
            $met = $_GET;
        } elseif ($metod == "POST") {
            $met = $_POST;
        } elseif ($metod == "REQ") {
            $met = &$_REQUEST;
        } elseif ($metod == "FILE") {
            $met = $_FILES;
        } elseif ($metod == "COOKIE") {
            $met = $_COOKIE;
        } else {
            return false;
        }

        return array_key_exists($name, $met);
    }

    public function setGet($name, $value = null) {
        static::setVar($name, $value, "GET");
    }

    public static function setVar($name, $value = null, $metod = "GET") {
        if ($metod == "GET") {
            $var = &$_GET;
        } elseif ($metod == "POST") {
            $var = &$_POST;
        } elseif ($metod == "REQ") {
            $var = &$_REQUEST;
        } elseif ($metod == "FILE") {
            $var = &$_FILES;
        } else {
            return false;
        }

        if ($value !== null) {
            $var[$name] = $value;
        } else {
            unset($var[$name]);
        }
    }

    public static function getVar($var, $metod = "GET", $filter = array("decode", "deleteteg", "html")) {
        if ($metod == "GET") {
            $met = $_GET;
        } elseif ($metod == "POST") {
            $met = $_POST;
        } elseif ($metod == "REQ") {
            $met = $_REQUEST;
        } elseif ($metod == "FILE") {
            $met = $_FILES;
        } elseif ($metod == "COOKIE") {
            $met = $_COOKIE;
        } else {
            return false;
        }

        if (is_array($var)) {
            $var = static::getArrayKey($var, $met);
        } else {
            $var = isset($met[$var]) ? $met[$var] : false;
        }

        if ($var === false)
            return false;

        if (is_array($filter)) {
            foreach ($filter as $f_name) {
                static::filterVar($var, $f_name);
            }
        } else {
            static::filterVar($var, $filter);
        }

        return $var;
    }

    public static function getMetod($metod, $filter = array("deleteteg")) {
        if ($metod == "GET") {
            $var = $_GET;
        } elseif ($metod == "POST") {
            $var = $_POST;
        } elseif ($metod == "URI") {
            $var = $_SERVER['REQUEST_URI'];
            return $var;
        } elseif ($metod == "REQ") {
            $var = $_REQUEST;
        } elseif ($metod == "FILE") {
            $var = $_FILES;
        } elseif ($metod == "COOKIE") {
            $var = $_COOKIE;
        }

        if (is_array($filter)) {
            foreach ($filter as $f_name) {
                static::filterVar($var, $f_name);
            }
        } else {
            static::filterVar($var, $filter);
        }

        return $var;
    }

    public static function filterVar(&$var, $filter) {
        switch ($filter) {
            case "decode":
                if (is_array($var)) {
                    $var = self::arrayRecursiveFilter($var, "url");
                } else {
                    $var = static::filterUrl($var);
                }
                break;

            case "rawdecod":
                if (is_array($var)) {
                    $var = static::arrayRecursiveFilter($var, "raw");
                } else {
                    $var = static::filterRaw($var);
                }
                break;

            case "html":
                if (is_array($var)) {
                    $var = static::arrayRecursiveFilter($var, "htmlscreen");
                } else {
                    $var = static::filterHtml($var);
                }
                break;

            case "deleteteg":
                if (is_array($var)) {
                    $var = static::arrayRecursiveFilter($var, "striptags");
                } else {
                    $var = static::stripTags($var);
                }
                break;

            case "slash":
                if (is_array($var)) {
                    $var = static::arrayRecursiveFilter($var, "addslashes");
                } else {
                    $var = static::filteraddSlashes($var);
                }
                break;

            case "none":
                $var;
                break;
        }

        return $var;
    }

    /*
      ///////////////////////////////////////////////////////////////////////
      // 					РЕКУРИВНЯ ФИЛЬТРАЦИЯ МАССИВОВ
      //
      ///////////////////////////////////////////////////////////////////////
     */

    public static function arrayRecursiveFilter($arr, $type) {
        $obj = Request::instance();
        $result = $arr;
        switch ($type) {
            case "htmlscreen":
                $filter = array(&$obj, 'filterHtml');
                break;
            case "striptags":
                $filter = array(&$obj, 'stripTags');
                break;
            case "url":
                $filter = array(&$obj, 'filterUrl');
                break;
            case "raw":
                $filter = array(&$obj, 'filterRaw');
                break;
            case "addslashes":
                $filter = array(&$obj, 'filteraddSlashes');
                break;
        }
        array_walk_recursive($result, $filter);
        return $result;
    }

    /*
      экранируем html теги
     */

    public static function filterHtml(&$value) {
        return $value = htmlspecialchars($value);
    }

    /*
      удаляем html теги
     */

    public static function stripTags(&$value) {
        return $value = strip_tags($value);
    }

    /*
      возвращает строку где все символы отличные от букв и цифр английского
      алфавита кодируются двумя шестнадцатиричными числами и предварительным сивалом %
      пробел занаком +
     */

    public static function filterUrl(&$value) {
        return $value = urldecode($value);
    }

    public static function filterRaw(&$value) {
        return $value = rawurldecode($value);
    }

    public static function filteraddSlashes(&$value) {
        return $value = addslashes($value);
    }

    /*
      активная ссылка
     */

    public function getActive() {
        return $this->mysql_escape(self::getMetod("URI"));
    }

    public function getOS($agent = null) {
        if ($agent == null)
            $agent = $_SERVER['HTTP_USER_AGENT'];

        $os = array("iPod", "iPhone", "Android", "Symbian",
            "WindowsPhone", "WP7", "WP8", "Opera M",
            "webOS", "BlackBerry", "Mobile", "HTC", "Fennec/",
            "macintosh", "linux", "windows");
        $result = $agent;

        foreach ($os as $oss) {
            if (stripos($agent, $oss) > 0) {
                $result = $oss;
                break;
            }
        }

        return $result;
    }

    public function gerBrowser($agent = null) {
        if ($agent == null)
            $agent = $_SERVER['HTTP_USER_AGENT'];

        if (preg_match("#Chrome/.+YaBrowser/.+#i", $agent)) {
            return "yandex";
        } elseif (preg_match("#Chrome/.+Safari/.+#i", $agent)) {
            return "chrome";
        } elseif (preg_match("#Version/.+Safari/.+#i", $agent)) {
            return "safari";
        } elseif (preg_match("#Opera/.+\(.+#i", $agent)) {
            return "opera";
        } elseif (stripos($agent, "MSIE 6.0") > 0) {
            return "ie6";
        } elseif (stripos($agent, "MSIE 7.0") > 0) {
            return "ie7";
        } elseif (stripos($agent, "MSIE 8.0") > 0) {
            return "ie8";
        } elseif (stripos($agent, "MSIE 9.0") > 0) {
            return "ie9";
        } elseif (stripos($agent, "Firefox") > 0) {
            return "firefox";
        } else {
            return false;
        }
    }

    /**
     * Returns the cookie collection.
     * Through the returned cookie collection, you may access a cookie using the following syntax:
     *
     * ~~~
     * $cookie = $request->cookies['name']
     * if ($cookie !== null) {
     *     $value = $cookie->value;
     * }
     *
     * // alternatively
     * $value = $request->cookies->getValue('name');
     * ~~~
     *
     * @return CookieCollection the cookie collection.
     */
    public function getCookies() {
        if ($this->_cookies === null) {
            $this->_cookies = new CookieCollection($this->loadCookies(), [
                'readOnly' => true,
            ]);
        }

        return $this->_cookies;
    }

    /**
     * Converts `$_COOKIE` into an array of [[Cookie]].
     * @return array the cookies obtained from request
     * @throws InvalidConfigException if [[cookieValidationKey]] is not set when [[enableCookieValidation]] is true
     */
    protected function loadCookies() {
        $cookies = [];
        foreach ($_COOKIE as $name => $value) {
            $cookies[$name] = new Cookie([
                'name' => $name,
                'value' => $value,
                'expire' => null
            ]);
        }

        return $cookies;
    }

    public function getHeaders() {
        if ($this->_headers === null) {
            $this->_headers = new HeaderCollection;
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->_headers->add($name, $value);
                    }
                }

                return $this->_headers;
            }
            foreach ($headers as $name => $value) {
                $this->_headers->add($name, $value);
            }
        }

        return $this->_headers;
    }

    public function getAcceptableContentTypes() {
        if ($this->_contentTypes === null) {
            if (isset($_SERVER['HTTP_ACCEPT'])) {
                $this->_contentTypes = $this->parseAcceptHeader($_SERVER['HTTP_ACCEPT']);
            } else {
                $this->_contentTypes = [];
            }
        }

        return $this->_contentTypes;
    }

    public function getAcceptableLanguages() {
        if ($this->_languages === null) {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $this->_languages = array_keys($this->parseAcceptHeader($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            } else {
                $this->_languages = [];
            }
        }

        return $this->_languages;
    }

    /**
     * Parses the given `Accept` (or `Accept-Language`) header.
     *
     * This method will return the acceptable values with their quality scores and the corresponding parameters
     * as specified in the given `Accept` header. The array keys of the return value are the acceptable values,
     * while the array values consisting of the corresponding quality scores and parameters. The acceptable
     * values with the highest quality scores will be returned first. For example,
     *
     * ```php
     * $header = 'text/plain; q=0.5, application/json; version=1.0, application/xml; version=2.0;';
     * $accepts = $request->parseAcceptHeader($header);
     * print_r($accepts);
     * // displays:
     * // [
     * //     'application/json' => ['q' => 1, 'version' => '1.0'],
     * //      'application/xml' => ['q' => 1, 'version' => '2.0'],
     * //           'text/plain' => ['q' => 0.5],
     * // ]
     * ```
     *
     * @param string $header the header to be parsed
     * @return array the acceptable values ordered by their quality score. The values with the highest scores
     * will be returned first.
     */
    public function parseAcceptHeader($header) {
        $accepts = [];
        foreach (explode(',', $header) as $i => $part) {
            $params = preg_split('/\s*;\s*/', trim($part), -1, PREG_SPLIT_NO_EMPTY);
            if (empty($params)) {
                continue;
            }
            $values = [
                'q' => [$i, array_shift($params), 1],
            ];
            foreach ($params as $param) {
                if (strpos($param, '=') !== false) {
                    list ($key, $value) = explode('=', $param, 2);
                    if ($key === 'q') {
                        $values['q'][2] = (double) $value;
                    } else {
                        $values[$key] = $value;
                    }
                } else {
                    $values[] = $param;
                }
            }
            $accepts[] = $values;
        }

        usort($accepts, function ($a, $b) {
            $a = $a['q']; // index, name, q
            $b = $b['q'];
            if ($a[2] > $b[2]) {
                return -1;
            } elseif ($a[2] < $b[2]) {
                return 1;
            } elseif ($a[1] === $b[1]) {
                return $a[0] > $b[0] ? 1 : -1;
            } elseif ($a[1] === '*/*') {
                return 1;
            } elseif ($b[1] === '*/*') {
                return -1;
            } else {
                $wa = $a[1][strlen($a[1]) - 1] === '*';
                $wb = $b[1][strlen($b[1]) - 1] === '*';
                if ($wa xor $wb) {
                    return $wa ? 1 : -1;
                } else {
                    return $a[0] > $b[0] ? 1 : -1;
                }
            }
        });

        $result = [];
        foreach ($accepts as $accept) {
            $name = $accept['q'][1];
            $accept['q'] = $accept['q'][2];
            $result[$name] = $accept;
        }

        return $result;
    }

}

?>