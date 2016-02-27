<?php defined('_CHECK_') or die("Access denied");
use maze\helpers\DataTime;

RC::import("@lib/smarty/libs/SmartyBC.class.php");

class TemplateMail {

    const DIR_SMARTY = "@root/temp/tpl";

    protected $_group;
    protected $_name;
    protected $_title;
    protected $_titlegroup;
    protected $_created;
    protected $_version;
    protected $_theme;
    protected $_author;
    protected $_license;
    protected $_siteauthor;
    protected $_email;
    protected $_descriptions;
    protected $content;
    protected $smapty;
    protected $simplexml;
    protected $langcode;

    public function __construct($type, $front, $path, $name, $group = false) {

       
        if (!file_exists($path)) {
            throw new Exception(Text::_("LIB_FRAMEWORK_APPLICATION_RC_IMPORT_NOFILE", array(basename($path), $path)));
        }

        libxml_use_internal_errors(true);
        $this->simplexml = simplexml_load_file($path, null, LIBXML_NOCDATA);

        if (!$this->simplexml) {
            throw new Exception(Text::_("LIB_FRAMEWORK_APPLICATION_LOADXML", array($path)));
        }

        $this->smapty = new SmartyBC();



        $pathSmar = RC::getAlias(self::DIR_SMARTY);

        if (!is_dir($pathSmar)) {
            throw new Exception(Text::_("LIB_FRAMEWORK_APPLICATION_RC_IMPORT_NOFILE", array($pathSmar, $pathSmar)));
        }

        $this->smapty->compile_dir = $pathSmar;

        $this->_group = isset($this->simplexml->group) ? $this->simplexml->group : null;

        $this->_name = isset($this->simplexml->name) ? $this->simplexml->name : null;

        $this->_title = isset($this->simplexml->title) ? $this->simplexml->title : null;

        $this->_titlegroup = isset($this->simplexml->titlegroup) ? $this->simplexml->titlegroup : null;

        $this->_created = isset($this->simplexml->created) ? $this->simplexml->created : null;

        $this->_version = isset($this->simplexml->version) ? $this->simplexml->version : null;

        $this->_author = isset($this->simplexml->author) ? $this->simplexml->author : null;

        $this->_license = isset($this->simplexml->license) ? $this->simplexml->license : null;

        $this->_siteauthor = isset($this->simplexml->siteauthor) ? $this->simplexml->siteauthor : null;

        $this->_email = isset($this->simplexml->email) ? $this->simplexml->email : null;

        $this->_descriptions = isset($this->simplexml->descriptions) ? $this->simplexml->descriptions : null;

        $this->_theme = isset($this->simplexml->theme) ? $this->simplexml->theme : null;

        $this->content = isset($this->simplexml->content) ? $this->simplexml->content : null;
        $conf = RC::getConfig();
        $this->smapty->assign("langcode", $this->langcode);
        $this->smapty->assign("siteurl", Request::siteUrl());
        $this->smapty->register_block("TEXT", [&$this, "Text"]);
        $this->smapty->register_modifier("getDate", [&$this, "getDate"]);
        $this->smapty->assign_by_ref('config', $conf);
    }

    public function __get($name) {
        $name = "_" . $name;

        if (isset($this->$name)) {
            return Text::_(trim($this->$name));
        }
        return false;
    }

    public function getXML() {
        return $this->simplexml;
    }

    public function getSmarty() {
        return $this->smapty;
    }

    public function getContent() {
        return $this->content;
    }

    public function loadTpl() {
        if ($this->content == null)
            return false;

        return $this->smapty->fetch('string:' . $this->content);
    }

    public function Text($params, $content, &$smarty, &$repeat) {
        return Text::_($content);
    }

    public function getDate($date, $format) {
        return DataTime::format($date, $format);
    }

}

?>