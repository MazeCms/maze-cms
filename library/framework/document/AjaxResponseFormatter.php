<?php

namespace maze\document;

use RC;
use maze\base\Object;
use maze\base\ResponseFormatterInterface;
use maze\helpers\Json;

class AjaxResponseFormatter extends Object implements ResponseFormatterInterface
{

    public $useJsonp = false;


    public function format($response)
    {
     
        
        if ($this->useJsonp) {
            $this->formatJsonp($response);
        } else {
            
            if(RC::app()->request->isJson()){                
                $this->formatJson($response);
            }else{
                $this->formatHtml($response);
            }
            
        }
        
    }

    /**
     * Formats response data in HTML format.
     * @param Response $response
     */
    protected function formatHtml($response)
    {
        $response->getDocument()->setHeader('Content-Type', 'text/html');
        
        if($response->data === null){
            $response->data = RC::app()->getView()->component;
        }
        $response->content = $response->data;
    }
    
    /**
     * Formats response data in JSON format.
     * @param Response $response
     */
    protected function formatJson($response)
    {
        $response->getDocument()->setHeader('Content-Type', 'application/json; charset=UTF-8');
        if($response->data === null){
            $response->data = Json::encode(RC::app()->getView()->renderPageJson());
        }elseif(is_array($response->data)){
            $response->data = Json::encode($response->data);
        }
        $response->content = $response->data;
    }

    /**
     * Formats response data in JSONP format.
     * @param Response $response
     */
    protected function formatJsonp($response)
    {
        $response->getDocument()->set('Content-Type', 'application/javascript; charset=UTF-8');
        if (is_array($response->data) && isset($response->data['data'], $response->data['callback'])) {
            $response->content = sprintf('%s(%s);', $response->data['callback'], Json::encode($response->data['data']));
        } else {
            $response->content = '';
            
        }
    }
}
