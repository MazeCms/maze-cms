<?php

namespace site\application;

use RC;
use maze\base\Object;
use maze\helpers\ArrayHelper;
use maze\exception\UserException;

class Component extends Object {

    protected $_name;
    
    protected $_config;    
    
    protected $_front;
    
    protected $_exp;
    
    protected $_render;


    public function init() {

    }

    public function setName($name){
        $this->_name = $name;
    }
    
    public function getName(){
        return $this->_name;
    }
    
    public function getId_tmp(){
        return $this->_name;
    }
    
    public function getId_exp(){
        return $this->_name;
    }
    
    public function getTime_cache(){
        return 15;
    }
    
    public function getEnable_cache(){
        return false;
    }
    
    public function getEnabled(){
        return true;
    }
    
    public function getPathIndex(){
        return RC::getAlias('@site/expansion/exp_'.$this->_name.'/'.$this->_name . ".php");
    }
    
    public function getPath($path = null){
        $path = $path ? DS.$path : '';
        return dirname($this->getPathIndex()).$path;
    }
    
    public function getUrl($url = null){
        $url = $url ? '/'.ltrim($url, '/') : '';
        return RC::getAlias('@web/install/expansion/exp_'.$this->_name.$url);
    }


    public function getIs(){        
        if (!file_exists($this->getPathIndex())) return false;
       
        return true;
    }
    
    public function run(){
        if($this->_render === null){
             ob_start();
            $out = include_once $this->getPathIndex();
            if(!is_string($out)) $out = '';
           
            $this->_render = ob_get_clean().$out;
        }
       return $this->_render;
    }
    
    

    public function getConfig() {
        return null;
    }

}

?>