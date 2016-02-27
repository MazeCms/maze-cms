<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\Html;
use maze\helpers\Json;
use maze\exception\UserException;
use maze\base\HeaderCollection;
use maze\base\CookieCollection;

class Document {

    const DOCREADY = 1;

    protected $_title;
    protected $_description;
    protected $_keywords;
    protected $_robots;
    protected $_author;
    protected $_metateg = array();
    protected $_link = [];
    protected $_charset;
    protected $_type;
    protected $_language;
    protected $_cache;
    protected $_favicon;
    protected $_bodyclass;
    protected $_htmlclass;
    protected $_cookies;
    protected $_scripts = array();
    protected $_textSprit = array();
    protected $_stylesheet = array();
    protected $_textCss = array();
    protected $_header = array();
    protected $asset;

    /**
     * @var bool - подавление кеширования скриптов
     */
    public $cacheScript = false;

    /**
     * @var bool - подавление кеширования таблицы стилей
     */
    public $cacheStyle = false;

    protected static $_instance;

    private function __clone() {
        
    }

    private function __construct() {
       
    }

    public static function instance() {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __set($index, $value) {
        $this->set($index, $value);
    }

    public function __get($index) {
        $this->get($index);
    }

    public function set($index, $value) {
        $index = "_" . $index;

        $allowed = array("_title", "_description", "_keywords",
            "_robots", "_author", "_charset", "_language", "_type", "_favicon");

        if (in_array($index, $allowed)) {
            $this->$index = $value;
            return $value;
        }
        return false;
    }

    public function get($index) {
        $index = "_" . $index;
        if (isset($this->$index)) {
            return $this->$index;
        }
        return false;
    }

    public function setBobyClass($classCss) {
        if (is_array($classCss)) {
            foreach ($classCss as $class) {
                $this->setBobyClass($class);
            }
        } else {
            if (!empty($classCss)) {
                $this->_bodyclass[] = $classCss;
            }
        }
    }

    public function getBodyClass() {
        return $this->_bodyclass ? implode(' ', $this->_bodyclass) : null;
    }

    public function setHtmlClass($classCss) {
        if (is_array($classCss)) {
            foreach ($classCss as $class) {
                $this->setHtmlClass($class);
            }
        } else {
            if (!empty($classCss)) {
                $this->_htmlclass[] = $classCss;
            }
        }
    }

    public function getHtmlClass() {
        return $this->_htmlclass ? implode(' ', $this->_htmlclass) : null;
    }

    public function getCookies() {
        if ($this->_cookies === null) {
            $this->_cookies = new CookieCollection;
        }

        return $this->_cookies;
    }

    public function setHeader($header, $value = "") {
        if ($this->_header == null) {
            $this->_header = new HeaderCollection;
        }

        if (is_array($header)) {
            foreach ($header as $name => $val) {
                $this->_header->add($name, $val);
            }
        } else {
            $this->_header->add($header, $value);
        }
        return $this;
    }

    public function getHeader() {
        return $this->_header;
    }

    public function noCache() {
        $header = array("Expires" => "Mon, 30 May 2000 02:00:00 GMT",
            "Last-Modified" => gmdate("D, d M Y H:i:s") . " GTM",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache");

        $this->_cache = "no-cache";
        $this->setHeader($header);
    }

    public function sendHeader() {

        
        foreach ($this->_header as $name => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
            $replace = true;
            foreach ($values as $value) {
                header("$name: $value", $replace);
                $replace = false;
            }
        }
    }

    public function addMetateg($meta) {
        if (is_array($meta)) {
            $this->_metateg[] = $meta;
        }
    }

    private function setMetateg() {
        $this->_metateg[] = array("name" => "description", "content" => $this->_description);
        $this->_metateg[] = array("name" => "keywords", "content" => $this->_keywords);
        $this->_metateg[] = array("name" => "robots", "content" => $this->_robots);
        if (RC::app()->config->show_author) {
            $this->_metateg[] = array("name" => "author", "content" => $this->_author);
        }
        $this->_metateg[] = array("http-equiv" => "content-type", "content" => $this->_type . "; charset=" . $this->_charset);
        $request = \RC::app()->request;
        if ($request->enableCsrfValidation) {
            $this->_metateg[] = array("name" => "csrf-param", "content" => $request->csrfParam);
            $this->_metateg[] = array("name" => "csrf-token", "content" => $request->getCsrfToken());
        }
    }

    public function getIsMetaTag($name, $val) {
        $result = false;
        foreach ($this->_metateg as $meta) {
            if (isset($meta[$name]) && $meta[$name] == $val) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function deleteMetaTag($name, $val) {
        $result = false;
        foreach ($this->_metateg as $key => $meta) {
            if (isset($meta[$name]) && $meta[$name] == $val) {
                unset($this->_metateg[$key]);
                break;
            }
        }
        return $result;
    }

    public function getMeta() {
        $this->setMetateg();

        $meta = array();
        foreach ($this->_metateg as $teg) {
            if (isset($teg["content"]) && !empty($teg["content"])) {
                $teg["content"] = htmlspecialchars($teg["content"], ENT_QUOTES | ENT_COMPAT, "UTF-8");
            }

            $metateg = Html::tag("meta", "", $teg);

            array_push($meta, $metateg);
        }
        return $meta;
    }

    public function addlinkTag($meta) {
        if (is_array($meta)) {
            $this->_link[] = $meta;
        }
    }

    public function getIsLinkTag($name, $val) {
        $result = false;
        foreach ($this->_link as $meta) {
            if (isset($meta[$name]) && $meta[$name] == $val) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    public function deleteLinkTag($name, $val) {
        $result = false;
        foreach ($this->_link as $key => $meta) {
            if (isset($meta[$name]) && $meta[$name] == $val) {
                unset($this->_link[$key]);
                break;
            }
        }
        return $result;
    }

    public function getLink() {
        $meta = array();
        foreach ($this->_link as $teg) {

            $metateg = Html::tag("link", "", $teg);

            array_push($meta, $metateg);
        }
        return $meta;
    }

    public function getTitle() {
        return Html::tag('title', $this->_title);
    }

    public function getFavicon() {

        $options = [];
        if (is_array($this->_favicon)) {
            $options = $this->_favicon;
        } else {
            $options = ['rel' => 'icon', 'type' => 'image/png', 'href' => $this->_favicon];
        }
        if (isset($options['href']) && !empty($options['href'])) {
            $options['href'] .= '?' . $this->randInt(2, 1, 9);
        } else {
            return false;
        }
        return Html::tag('link', '', $options);
    }

    public function registerAssetBundle($class, array $args = []) {
        if (isset($this->asset[$class]))
            return;

        if (class_exists($class)) {
            $refClass = new ReflectionClass($class);

            if ($refClass->isSubclassOf('\maze\document\AssetBundle')) {
                $this->asset[$class] = RC::createObject(array_merge($args, ['class' => $class]));
                $this->asset[$class]->registerAssetFiles();
            } else {
                throw Exception("Класс " . $class . " не наследует maze\document\AssetBundle");
            }
        } else {
            throw Exception("Класс " . $class . " не сушествует");
        }
        return $this;
    }

    public function getAsset($class) {
        if (isset($this->asset[$class])) {
            return $this->asset[$class];
        }
    }

    public function addScript($src, $options = []) {
        if (!is_array($options))
            $options = [];

        $options = array_merge(["type" => "text/javascript", "cache" => true], $options);
        $url = new URI($src);
        if ($options['cache'] && $this->cacheScript) {
            $url->setVar('__', $this->randInt(4, 1, 9));
        }
        $options["src"] = $url->toString();
        $options["sort"] = (isset($options["sort"])) ? $options["sort"] : count($this->_scripts) - 1;
        $this->_scripts[] = $options;
        return $this;
    }

    public function addStylesheet($href, $options = []) {

        if (!is_array($options))
            $options = [];

        $options = array_merge(["type" => "text/css", "rel" => "stylesheet", "cache" => true], $options);

        $url = new URI($href);

        if ($options['cache'] && $this->cacheStyle) {
            $url->setVar('__', $this->randInt(4, 1, 9));
        }
        $options["href"] = $url->toString();

        $options ["sort"] = (isset($options["sort"])) ? $options ["sort"] : count($this->_stylesheet) - 1;
        $this->_stylesheet[] = $options;
        return $this;
    }

    public function setLangTextScritp($obj, $handler = "cms.setLang") {
        if (is_string($obj)) {
            $obj = explode(",", $obj);
        }
        if (!is_array($obj))
            return false;

        $result = [];

        foreach ($obj as $lang) {
            $result[$lang] = Text::_($lang);
        }
        $result = $handler . "(" . Json::encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ")";

        $this->setTextScritp($result);
    }

    /**
     * сортировка по убыванию относительно элемента массива ['ordering']
     * @param type $arr
     * @return type
     */
    public function ordering($arr) {
        usort($arr, function($a, $b) {
            $a = intval($a['sort']);
            $b = intval($b['sort']);

            if ($a == $b) {
                return 0;
            }

            return ($a > $b) ? -1 : 1;
        });

        return $arr;
    }

    public function randInt($whatInt, $min, $max) {
        $result = "";
        for ($i = 0; $i < $whatInt; $i++) {
            $result .= rand($min, $max);
        }
        return $result;
    }

    public function getSctipts() {

        $arr = $this->ordering($this->_scripts);
        
        RC::getPlugin("system")->triggerHandler("beforeGetScripts", [&$arr]);
        
        $scripts = [];
        foreach ($arr as $script) {
            unset($script['sort']);
            unset($script['cache']);
            $scripts[] = Html::tag('script', '', $script);
        }
        return $scripts;
    }

    

    public function getObjSctipts() {
        $arr = $this->ordering($this->_scripts);
        $scripts = array();
         RC::getPlugin("system")->triggerHandler("beforeGetScripts", [&$arr]);
        foreach ($arr as $script) {
            $teg = array();
            foreach ($script as $name => $cotnent) {
                if (in_array($name, ["sort", "cache"]))
                    continue;
                $teg[$name] = $cotnent;
            }

            array_push($scripts, $teg);
        }


        return $scripts;
    }

    public function getStylesheet() {
        $arr = $this->ordering($this->_stylesheet);
        $style = [];
        RC::getPlugin("system")->triggerHandler("beforeGetStylesheet", [&$arr]);
         
        foreach ($arr as $link) {
           if(isset($link['sort'])) unset($link['sort']);
           if(isset($link['cache'])) unset($link['cache']);
            $style[] = Html::tag('link', '', $link);
        }

        return $style;
    }

    public function getObjStylesheet() {
        $arr = $this->ordering($this->_stylesheet);
        $style = array();
        RC::getPlugin("system")->triggerHandler("beforeGetStylesheet", [&$arr]);
        foreach ($arr as $script) {
            $teg = array();
            foreach ($script as $name => $cotnent) {
                if (in_array($name, ["sort", "cache"]))
                    continue;
                $teg[$name] = $cotnent;
            }

            array_push($style, $teg);
        }
        return $style;
    }

    public function setTextScritp($text, $options = []) {
        $this->_textSprit[] = ['content' => $text, 'options' => $options];
    }

    public function getTextScritp() {
        if (empty($this->_textSprit))
            return false;

        $str = [];
        $ready = [];
        foreach ($this->_textSprit as $text) {
            if (isset($text['options']['wrap'])) {
                switch ($text['options']['wrap']) {
                    case self::DOCREADY:
                        $ready[] = $text['content'];
                        break;
                }
            } else {
                $str[] = $text['content'];
            }
        }


        if (!empty($ready)) {
            $str[] = "jQuery(document).ready(function(){" . implode(";\n", $ready) . "}); ";
        }

        return Html::tag('script', implode("\n", $str), ['type' => 'text/javascript']);
    }

    public function getObjTextScritp() {
        if (empty($this->_textSprit))
            return false;

        $teg["type"] = "text/javascript";
        $str = [];
        $ready = [];
        foreach ($this->_textSprit as $text) {
            if (isset($text['options']['wrap'])) {
                switch ($text['options']['wrap']) {
                    case self::DOCREADY:
                        $ready[] = $text['content'];
                        break;
                }
            } else {
                $str[] = $text['content'];
            }
        }

        if (!empty($ready)) {
            $str[] = 'jQuery(document).ready(function(){' . implode("\n", $ready) . '});';
        }
        $teg["innerHTML"] = implode(' ', $str);
        return $teg;
    }

    
    public function setTextCss($text) {
        array_push($this->_textCss, $text);
    }

    public function getTextCss() {
        if (empty($this->_textCss))
            return false;

        $teg = "<style type=\"text/css\">\n";

        foreach ($this->_textCss as $text) {
            $teg .= $text . "\n";
        }

        $teg .= "</style>";
        return $teg;
    }

    public function getObjTextCss() {
        if (empty($this->_textCss))
            return false;


        $teg["type"] = "text/css";
        $teg["innerHTML"] = "";

        foreach ($this->_textCss as $text) {
            $teg["innerHTML"] .= $text . "\n\n";
        }

        return $teg;
    }

    public function setMessage($text, $type) {
        if (is_array($text)) {
            $allText = '<ul>';
            foreach ($text as $attr => $mess) {
                if (is_array($mess)) {
                    foreach ($mess as $t) {
                        $allText .= '<li>' . Text::_($t) . '</li>';
                    }
                } else {
                    $allText .= '<li>' . Text::_($mess) . '</li>';
                }
            }
            $allText .= '</ul>';
            $text = $allText;
        } else {
            $text = Text::_($text);
        }

        RC::app()->session->set('message', array('text' => $text, 'type' => $type), defined('ADMIN') ? "admin" : "site");
    }

    public function getMessage($front = null) {
        if ($front === null) {
            $front = defined('ADMIN') ? "admin" : "site";
        } else {
            $front = $front ? "site" : "admin";
        }
        $message = RC::app()->session->get('message', $front);
        if (!RC::app()->response->getIsRedirection()) {
            RC::app()->session->clear('message', $front);
            return $message;
        }
    }

    public function setRedirect($url, $statusCode = 302) {
        RC::app()->getResponse()->redirect($url, $statusCode);
    }

}

?>
