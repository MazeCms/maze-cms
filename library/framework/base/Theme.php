<?php

namespace maze\base;

use RC;
use maze\helpers\FileHelper;
use ui\assets\AssetTheme;

class Theme extends Object {

    /**
     * @var array - переопределяемые пути шаблонов темизации 
     * оригинальный путь шаблона=>где искать шаблон в директории темы
     */
    public static $pathMap = [
        '@site/templates/system/widgets' => 'views/widgets',
        '@site/expansion' => 'views/expansion',
        '@tmp/system/clear/'=>'/',
        '@site/widgets' => 'views/widgets',
        '@tmp/system/message'=>'views/message',
        '@tmp/system/toolbar' => 'views/toolbar',
        '@tmp/system/mail'=>'views/mail'
    ];
    protected $param;
    protected $name;
    protected $front;
    protected $assets;

    /**
     * Initializes the theme.
     * @throws InvalidConfigException if [[basePath]] is not set.
     */
    public function init() {
        parent::init();
        
        $this->assets = AssetTheme::register([
            'basePath'=>$this->getBasePath().'/assets',
            'baseUrl'=>$this->getBaseUrl().'/assets'
        ]);
    }

    private $_baseUrl;

    /**
     * @return string the base URL (without ending slash) for this theme. All resources of this theme are considered
     * to be under this base URL.
     */
    public function getBaseUrl() {
        return $this->_baseUrl;
    }

    /**
     * @param $url string the base URL or path alias for this theme. All resources of this theme are considered
     * to be under this base URL.
     */
    public function setBaseUrl($url) {
        $this->_baseUrl = rtrim(RC::getAlias($url), '/');
    }

    private $_basePath;

    /**
     * @return string the root path of this theme. All resources of this theme are located under this directory.
     * @see pathMap
     */
    public function getBasePath() {
        return $this->_basePath;
    }

    public function getParam() {       
        return RC::getConf(["name" => $this->name, "type" => "template", "front" => $this->front], $this->param);
    }

    public function setParam($param) {
        $this->param = $param;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }

    public function setFront($front) {
        $this->front = $front;
    }

    public function applyTo($path) {
        $result = false;
        foreach (static::$pathMap as $from => $to) {
            if ($to) {
                $target = trim($to, '\//');
                $to = $this->getBasePath() . DS . $target;
            } else {
                $to = $this->getBasePath();
            }
            
            $search = RC::getAlias($from);
            
            
            if (strncmp($path, $search, mb_strlen($search)) === 0) {
                $file = str_replace($search, $to, $path);
                if (file_exists($file)) {
                    $result = $file;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $path the root path or path alias of this theme. All resources of this theme are located
     * under this directory.
     * @see pathMap
     */
    public function setBasePath($path) {
        $this->_basePath = RC::getAlias($path);
    }

    /**
     * Converts a relative URL into an absolute URL using [[baseUrl]].
     * @param string $url the relative URL to be converted.
     * @return string the absolute URL
     * @throws InvalidConfigException if [[baseUrl]] is not set
     */
    public function getUrl($url) {
        if (($baseUrl = $this->assets->getAssetBaseUrl()) !== null) {
            return $baseUrl . '/' . ltrim($url, '/');
        } else {
            throw new \Exception('The "baseUrl" property must be set.');
        }
    }

    /**
     * Converts a relative file path into an absolute one using [[basePath]].
     * @param string $path the relative file path to be converted.
     * @return string the absolute file path
     * @throws InvalidConfigException if [[baseUrl]] is not set
     */
    public function getPath($path) {
        if (($basePath = $this->getBasePath()) !== null) {
            return $basePath . DS . ltrim($path, '/\\');
        } else {
            throw new \Exception('The "basePath" property must be set.');
        }
    }

}
