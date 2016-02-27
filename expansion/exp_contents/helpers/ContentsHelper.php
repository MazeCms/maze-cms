<?php

namespace exp\exp_contents\helpers;
use RC;

class ContentsHelper{
    
    public static function getLayoutViewContent($contents, $default = ''){
        
        $layouts = [
            'type-'.$contents->bundle,
            'type-'.$contents->bundle.'-'.$contents->contents_id
        ];
        
        $result = static::getLayoutView($layouts);
        
        return $result ? $result : $default;
    }
    
    public static function getLayoutViewType($type, $default = ''){
        
        $result = static::getLayoutView(['type-'.$type]);
        
        return $result ? $result : $default;
    }
    
    public static function getLayoutViewCatalog($type, $default = ''){
        
        $result = static::getLayoutView(['catalog-'.$type]);
        
        return $result ? $result : $default;
    }
    
    public static function getLayoutViewTerm($contents, $default = ''){
        
        $layouts = [
            'type-'.$contents->bundle,
            'type-'.$contents->bundle.'-'.$contents->term_id
        ];
        
        $result = static::getLayoutView($layouts);
        
        return $result ? $result : $default;
    }
    
    public static function getLayoutView(array $layouts = []){
        $result = null;
        $themeName = RC::app()->theme->getName();
        $view = RC::app()->view;
        foreach($layouts as $layout){ 
   
            if($view->hasView('@tmp/'.$themeName.'/views/expansion/exp_contents/views/'.RC::app()->router->view.'/tmp/'.$layout)){
                $result = $layout;
                break;
            }elseif($view->hasView('/'.$layout)){
                $result = $layout;
                break;
            }
        }
        
        return $result;
    }
}
