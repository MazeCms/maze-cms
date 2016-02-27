<?php

namespace maze\document;

use RC;
use maze\base\Object;
use maze\helpers\FileHelper;

class AssetBundle extends Object {

    public $basePath;
    
    public $baseUrl;
    
    public $depends = [];
    
    public $js = [];
    
    public $css = [];
    
    public $jsOptions = [];
    
    public $cssOptions = [];
    
    public $publishOptions = [];
    
    /**
     * @var string - путь где хранятся ресурсы
     */
    public $pathAsstes = '@root/assets';

    public static function register($args = []) {
        RC::app()->document->registerAssetBundle(get_called_class(), $args);
        return RC::app()->document->getAsset(get_called_class());
    }

    public function init() {

        if ($this->basePath !== null) {
            $this->basePath = rtrim(RC::getAlias($this->basePath), '/\\');
        }
        if ($this->baseUrl !== null) {
            $this->baseUrl = rtrim(RC::getAlias($this->baseUrl), '/');
        }
        
        $this->createAssetsPath();
    }

    public function registerAssetFiles() {
        $baseUrl = $this->getAssetBaseUrl();
        $basePath = $this->getAssetBasePath();
        if (is_array($this->js)) {
            foreach ($this->js as $key => $js) {

                if (!file_exists($basePath . '/' . ltrim($js, '/'))) {
                    continue;
                }

                if (isset($this->jsOptions['sort']) && $key) {
                    $this->jsOptions['sort'] --;
                }


                RC::app()->document->addScript($baseUrl. '/' . ltrim($js, '/'), $this->jsOptions);
            }
        }
        if (is_array($this->css)) {
            foreach ($this->css as $key => $css) {
                if (!file_exists($basePath . '/' . ltrim($css, '/'))) {
                    continue;
                }
                if (isset($this->cssOptions['sort']) && $key) {
                    $this->cssOptions['sort'] --;
                }
                RC::app()->document->addStylesheet($baseUrl . '/' . ltrim($css, '/'), $this->cssOptions);
            }
        }
        foreach ($this->depends as $depends) {
            if (is_array($depends)) {
                if (count($depends) == 2) {
                    RC::app()->document->registerAssetBundle($depends[0], $depends[1]);
                }
            } else {
                RC::app()->document->registerAssetBundle($depends);
            }
        }
    }
    protected function createAssetsPath() {
        if($filePath = $this->getAssetBasePath()){
            if (!is_dir($filePath)){ 
                FileHelper::createDirectory($filePath, 0777, true);
                FileHelper::copy($this->basePath, $filePath, ['dirMode'=>0777]);
                
            }
            if(!file_exists(RC::getAlias($this->pathAsstes.'/.htaccess'))){
                file_put_contents(RC::getAlias($this->pathAsstes.'/.htaccess'), "Options All -Indexes");
            }
        }
    }
    public function getNameAsset() {
        return  sprintf('%x', crc32(RC::getAlias($this->basePath)));
    }
    
    public function getAssetBasePath(){
        return RC::getAlias(trim($this->pathAsstes, '/\\').'/'.$this->getNameAsset());
    }
    
    public function getAssetBaseUrl(){
       $path = RC::getAlias(trim($this->pathAsstes, '/\\'));
       $result = trim(str_replace(RC::getAlias('@root'), '', $path), '/\\');
       return '/'.$result.'/'.$this->getNameAsset();
    }

}
