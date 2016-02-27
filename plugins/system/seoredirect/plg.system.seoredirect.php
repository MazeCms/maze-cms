<?php

use maze\table\DictionaryTerm;
use maze\fields\FieldHelper;

class Seoredirect_Plugin_System extends Plugin {

    public function beforeDispatcher() {
        
        if(RC::app()->response->getIsRedirection()){
            return false;
        }
        $redirectFlag = false;
        $url = URI::instance()->toString(['path', 'query']);
        $request = RC::app()->request;
        $domain = mb_strtolower($request->getDomain());
        $domain = explode('.', $domain);
        
        // Запрет поддомена www
        if(count($domain) > 2){
            $alias = array_shift($domain);
            if($alias == 'www' && $this->params->getVar("www")){
               $url = $request->getProtocol() . implode('.', $domain);
            $url .= URI::instance()->toString(['path', 'query', 'fragment']);
            $redirectFlag = true;  
            }  
        }
        
        if(URI::instance()->toString(['path', 'query']) !== '/'){
            
            //Запрет index.php
            if(preg_match("/^((http:\/\/|https:\/\/)?([a-z0-9\-\.]+)?[a-z0-9\-]+(!?\.[a-z]{2,4}))?\/?index\.[a-z]{2,}/i", $url)  && $this->params->getVar("noindex")){
              $url = preg_replace("/^((http:\/\/|ftp:\/\/)?([a-z0-9\-\.]+)?[a-z0-9\-]+(!?\.[a-z]{2,4}))?\/?index\.[a-z]{2,}/i", "",$url);
              $redirectFlag = true;
            }

            
            //Использовать слеш в конце url
            if($url !== "/" && $this->params->getVar("slaches") && mb_substr($url, -1,1) == '/'){
                $redirectFlag = true;
                $url = rtrim($url, '/');
            }
            //Запретить суффиксы (.php, .html...)
            $suffix = preg_split("/,[\s]+|,/s", $this->params->getVar("suffix"));
            
            $suffix = implode("|",array_map('mb_strtolower', $suffix));
            if($suffix && preg_match("/.+(\.".$suffix.")\/?/i",URI::instance()->toString(['path']), $matches)){                      
                $url = str_replace($matches[1], '', $url);
                $redirectFlag = true;
            }
           
            
        }
        
       
        if($this->params->getVar("canonical") && $this->params->getVar("pageparam")){
             $pageUrl = new URI($url);
            if($pageUrl->hasVar($this->params->getVar("pageparam"))){
                $pageUrl->delVar($this->params->getVar("pageparam"));
                if(empty($pageUrl->getScheme())){
                    $pageUrl->setScheme($request->getIsSecureConnection()?'https':'http');
                }
                if(empty($pageUrl->getHost())){
                    $pageUrl->setHost($request->getDomain());
                }
                RC::app()->document->addlinkTag(['rel'=>'canonical', 'href'=>$pageUrl->toString()]);
            }
            
        }
        
        if(mb_substr($url,0,1) == "?"){
          $url = $request->getBaseUrl()."/".$url;
        }
        
        $url = empty($url) ? $request->getBaseUrl() : $url;

        if($redirectFlag){
            RC::app()->document->setRedirect($url, 301);  
        }
        
    }


}

?>