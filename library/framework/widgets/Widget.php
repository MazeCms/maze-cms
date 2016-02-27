<?php
namespace maze\widgets;

defined('_CHECK_') or die("Access denied");
use maze\base\Object;
use RC;

class Widget extends Object  implements \maze\base\ViewContextInterface{

    public $time_cache;
    
    public $id;
     
    public $enable_cache;
    
    public $position;
    
    public $name;
    
    public $panel;
    
    public $param;
    
    protected $front;
    
    protected $router;
    
    protected $params;
    
    protected $cache;
    
    public function init() {
         $this->front = defined("SITE") ? 1 : 0;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getDocument()
    {
        return \RC::app()->document;
    }

    public function getCache()
    {
        if($this->cache == null)
        {
           $this->cache = RC::getCache("wid_" . $this->name);
           $this->cache->setTimeLive($this->time_cache);
           $this->cache->setEnable($this->enable_cache);
        }
        
        return $this->cache;
    }
    
    public function getParams()
    {
       if($this->params == null)
       {
           $this->params = RC::getConf(array("name" => $this->name, "type" => "widget", "id" =>$this->id), $this->param);
       }
       
       return  $this->params;
    }
    
    public function getPath($path = null)
    {
        $path = $path ? '/'.ltrim($path, '/\\') : '';
        return RC::getAlias('@site/widgets/wid_'.$this->name.$path);
    }
    
    public function getUrl($url = null){
        $url = $url ? '/'.ltrim($url, '/\\') : '';
        return RC::getAlias('@web/'.($this->front ? '' : 'admin/').'widgets/wid_'.$this->name.$url);
    }
    
    public function getViewPath(){
        return $this->getPath();
    }


    public function isWidget()
    {
        $path = $this->getPath();
        
        return file_exists($path.DS . $this->name . ".php");
    }
    
    public function render($path, $vars = [])
    {
       return RC::app()->view->render($path, $vars, $this);
    }
    
    public function run()
    {
        $path = $this->getPath();
        $path .= DS.$this->name.".php";
        ob_start();
        include $path;
        return ob_get_clean();
    }

}

?>