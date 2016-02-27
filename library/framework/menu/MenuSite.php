<?php
namespace maze\menu;
use maze\base\Object;
use maze\helpers\ArrayHelper;

class MenuSite extends Object{

    private static $instance;
    
    protected $views;
    
    protected $exp;
    
    protected $app;
    
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getExp() {        
        if ($this->exp == null) {
            $this->exp = \maze\table\Expansion::find()
                    ->innerJoinWith('installApp')
                    ->andOnCondition(['ia.front_back' => 1])
                    ->orderBy('ia.ordering')
                    ->all();
        }

        return $this->exp;
    }

    public function getViews() {
        if($this->views !== null) return $this->views;
       
        foreach ($this->getExp() as $exp) {            
            $this->getView($exp->name);
        }

        return $this->views;
    }

    public function getView($name) {
        
        if(isset($this->views[$name])) return $this->views[$name];
        
        $this->views[$name] = [];
        
        $path = PATH_ROOT . DS . 'expansion' . DS . "exp_" . $name . DS . 'views';
        
        if (!is_dir($path)) return;
                
        $view = [];
        $dir = opendir($path);

        while (($file = readdir($dir)) !== false) {
            if (is_dir($path . DS . $file) && $file !== "." && $file !== "..") {
                array_push($view, $file);
            }
        }
         
        if(empty($view)) return null;
       
        foreach ($view as $meta) {
            $pathView = $path . DS . $meta . DS . "meta" . DS . "meta." . $meta . ".xml";
            if (!file_exists($pathView)) continue;
            $conf = new \XMLConfig($pathView);
            $xml = $conf->getXML();
            
            $layoutset = [];
           
            foreach($xml->layoutset->layout as $layout)
            {
                 $layoutset[] = (array)$layout;
            }
         
            $this->views[$name][$meta] = \RC::createObject([
                    'class' => 'maze\menu\View',
                    'view' =>$meta,
                    'appName'=>$name,
                    'title'=>(string)$conf->get("title"),
                    'description'=>(string)$conf->get("description"),
                    'layoutset'=> $layoutset                   
                ]);
        } 
        
        return $this->views[$name];
    }
    
    public function getApp()
    {
        if($this->app !== null) return $this->app;
        
        foreach ($this->getExp() as $exp) {            
            $this->app[$exp->name] = \RC::getConf(array("type" => "expansion", "name" =>$exp->name));  
        }
        
       return $this->app;
    }
}

?>