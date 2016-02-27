<?php

defined('_CHECK_') or die("Access denied");

use maze\exception\NotFoundHttpException;

class InstallRouter extends Router {

     public function getUrlparse() {
        $url = parent::getUrlparse();
        array_shift($url);
        return $url;
    }
   
    /**
     * ДИСПЕТЧЕР МАРШРУТОВ
     */
    public function dispatcher() {
        
        $url = $this->getUrlparse(); // исходный массив url	
        
       
        if (!$this->getIsHome()) {
            $link = count($url) > 1 ? $url[0] : $url[0];
        } else {
            $link = "install";
        }
 
        if(!$this->setExp($link)){
            throw new NotFoundHttpException(Text::_('Компонента с таким ({name}) именем не существует', ['name'=>$link]));
        }
      
       
        $this->getRoutes();
        
    }

}

?>