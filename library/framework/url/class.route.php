<?php defined('_CHECK_') or die("Access denied");
use RC;

class Route {

    public static function _($url, $html = null) {
        
        if(is_string($url) && mb_strpos($url, 'http') === 0){
            return $url;
        }
        if($url == '#') return '#';
        
        $uri = is_array($url) ? new URI($url) :  URI::instance($url);
        $cong = RC::getConfig();

        $path = $uri->getPath();

        $path = preg_replace(array("#^\/?index\.[a-zA-Z-0-9]+#i", "#\/*$#i", "#\.[a-zA-Z-0-9]{2,6}$#i"), "", $path);

        if ($cong->get("enab_prefix") && !empty($path)) {
            $path .= "." . $cong->get("prefix");
        }

        $uri->setPath($path);

        $url = $uri->toString(array('path', 'query', 'fragment'));

        if (!preg_match('#^/#', $url)) {
            $url = '/' . $url;
        }

        $url = preg_replace('/\s/u', '%20', $url);

        if ($html) {
            $url = htmlspecialchars($url);
        }

        return $url;
    }
    
    public static function to($path, $params = []) {
       
        $url = RC::app()->getRouter()->createRoute($path, $params);
       
        $url = new URI($url);
        $cong = RC::getConfig();

        $pathUrl = $url->getPath();

        if ($cong->get("enab_prefix") && !empty($pathUrl)) {
            $pathUrl .= "." . $cong->get("prefix");
        }

        $url->setPath($pathUrl);

        
        $url = $url->toString(['path', 'query', 'fragment']);
         if (!preg_match('#^/#', $url)) {
            $url = '/' . $url;
        }
        return $url;
    }

}

?>