<?php


namespace maze\document;

use RC;
use maze\base\Object;
use maze\base\ResponseFormatterInterface;


class HtmlResponseFormatter extends Object implements ResponseFormatterInterface
{

    public $contentType = 'text/html';

    public $useIframe = false;

    public function format($response)
    {
          
        if (stripos($this->contentType, 'charset') === false) {
            $this->contentType .= '; charset=' . $response->charset;
        }
        
        if($this->useIframe && $response->getIsSuccessful()){
            RC::app()->getView()->layout = '@tmp/system/clear/iframe';
            RC::app()->document->setHtmlClass('layout-iframe');
        }else{
             RC::app()->document->setHtmlClass('layout-index');
        }
        
        if($response->data === null){
            $response->data = RC::app()->getView()->renderPage();
        }
       
        $response->getDocument()->setHeader('Content-Type', $this->contentType);
        $response->content = $response->data;
    }
}
