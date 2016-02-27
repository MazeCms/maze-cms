<?php

defined('_CHECK_') or die("Access denied");
use maze\helpers\ArrayHelper;
use maze\helpers\Html;

class XMLConfig {

    protected $value = array();
    protected $params;
    protected $menu;
    protected $path;
    protected $simplexml;

    public function __construct($path, $value = false) {
         
        if (!file_exists($path)) {
            throw new maze\exception\Exception(Text::_("Текущего файла (" . $path . ") конфигурации не существует "));
        }

        libxml_use_internal_errors(true);

        $this->simplexml = simplexml_load_file($path, null, LIBXML_NOCDATA);

        if (!$this->simplexml) {
            throw new maze\exception\Exception(Text::_("LIB_FRAMEWORK_APPLICATION_LOADXML",[$path]));
        }
        $this->setValue($value);
    }

    public function getMenu() {
        if (isset($this->simplexml->menu)) {
            return $this->simplexml->menu;
        }
        return false;
    }
    
    public function getUrl()
    {
        $menu = $this->getMenu();
        return Route::_((string)$menu['path']);
    }

    public function getParams() {
        if (isset($this->simplexml->params)) {
            return $this->simplexml->params;
        }

        return false;
    }

    public function getXML() {
        return $this->simplexml;
    }

    public function setXML($xml) {
        $this->simplexml = $xml;
    }

    public function mergeXML(&$base, $add) {
        $new = $base->addChild($add->getName(), $add);

        foreach ($add->attributes() as $a => $b) {
            $new[$a] = $b;
        }
        foreach ($add->children() as $child) {
            $this->mergeXML($new, $child);
        }
    }

    public function get($index) {
        
        if (isset($this->simplexml->$index)) {          
            return  Text::_($this->simplexml->$index);
        }
        return false;
    }

    public function getVar($name) {
        $name = trim($name);
        $element = $this->simplexml->xpath("//element[@name='" . $name . "']");

        $value = false;
        if (isset($element[0])) {
            $element = $element[0]->attributes();

            if (isset($element["default"])) {
                $value = (string) $element["default"];

                if (!(strpos($value, "|") === false)) {
                    $resalt = explode("|", $value);
                    $value = array();
                    foreach ($resalt as $res)
                        $value[] = trim($res);
                }
            }
        }

       
        if ($this->value) {
            if (is_array($this->value)) {

                if (isset($this->value[$name])) {
                    $value = $this->value[$name];
                }
            } elseif (is_object($this->value)) {
                if (isset($this->value->$name)) {
                    $value = $this->value->$name;
                }
            }
         
        }

        return $value;
    }

    public function merge($params) {
        if (is_array($params)) {
            $this->value = array_merge(is_array($this->value) ? $this->value : [], $params);
        }
        return $this->value;
    }

    public function getValue() {
        return $this->value;
    }
    
    public function setValue($value)
    {
        if (is_string($value)) {
            $value = unserialize($value);
        }
        
        $this->value = $value;
        return $this->value;
    }
   
    public function elemenet($element, $namespace = "params", $arguments = []) {
        if (!isset($element["type"]) && empty($element["type"]))
            return false;
        
        $classname = (string) $element["type"];
        
      
        if (!class_exists($classname)) {
            return false;
        }
        
        if(is_object($namespace)){
            $arguments["name"] = Html::getInputName($namespace, (string)$element["name"]);
            $arguments['model'] = $namespace;
            $arguments['attribute'] = (string)$element["name"];
        }else{
            $arguments["name"] = $namespace . "[" . $element["name"] . "]";
        }
        
        $arguments["value"] = $this->getVar(isset($element["name"]) ? $element["name"] : "" );

       
        if($element->children()->count() > 0)
        {
            $arguments = array_merge($arguments, static::parseElelementOptions($element->children()));
        }
       $refClass = new ReflectionClass($classname);
        if(!$refClass->isSubclassOf('ui\Elements'))
        {
            throw new maze\exception\Exception(Text::_("Елемент  ".$element["type"]."::element() должен наследовать класс ui\Elements"));
        }
         
        return $classname::element($arguments);

    }
    
    public static function parseElelementOptions($obj)
    {
        $result = array();
        if(is_object($obj))
        {
            foreach($obj as $name=>$val)
            {
                if($val->attributes()->count() > 0 && $val->children()->count() == 0)
                {                  
                    $val = static::parseElementValue($val);            
                }
                elseif(is_object($val) && $val->children()->count() > 0)
                {
                    $val = static::parseElelementOptions($val);
                }
                else
                {
                    $val = (string)$val;
                }
                if(isset($result[(string)$name]))
                {
                    if(ArrayHelper::isIndexed($result[(string)$name]))
                    {
                        $result[(string)$name][] = $val;
                    }
                    else
                    {
                        $valOld = $result[(string)$name];
                        $result[(string)$name] = array();
                        $result[(string)$name][] = $valOld;
                        $result[(string)$name][] = $val;
                    }
                }
                else
                {
                  $result[(string)$name] = $val;  
                }
                
            }
        }
        
        return  $result;
        
    }
    
    protected static function parseElementValue($obj)
    {
        $result = array();
        foreach ($obj->attributes() as $name=>$value)
        {
            $result[(string)$name] = (string)$value;
        }
        $result['label'] = (string)$obj;
         
        return $result;
    }

}

?>