<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author nick
 */

namespace maze\log;

use maze\helpers\VarDumper;

class MessageRequest extends \maze\log\Message{
   
    /**
     * @var string -  маршрут
     */
    public $route;
    /**
     * @var string - название действия
     */
    public $action;
    
    /**
     * @var string - контроллер
     */
    public $controller;

    /**
     * @var int statusCode - код ответа 
     */
    public $statusCode;
    
    /**
     * @var int statusText -  статус ответа
     */
    public $statusText;
    
    /**
     * @var string requestHeaders -  заголовки запроса
     */
    public $requestHeaders;
    
    /**
     * @var string requestHeaders -  заголовки ответа
     */
    public $responseHeaders;


    public $get;
    
    public $post;

    public $cookie;
    
    public $session;
    
    public $server;


    public function getMessage(){

        if (!is_string($this->post)) {
            $this->post = serialize($this->post);
        }
        if (!is_string($this->get)) {
            $this->get = serialize($this->get);
        }
        if (!is_string($this->cookie)) {
            $this->cookie = serialize($this->cookie);
        }
        if (!is_string($this->requestHeaders)) {
            $this->requestHeaders = serialize($this->requestHeaders);
        }
        if (!is_string($this->responseHeaders)) {
            $this->responseHeaders = serialize($this->responseHeaders);
        }
        if (!is_string($this->session)) {
            $this->session = serialize($this->session);
        }
        if (!is_string($this->server)) {
            $this->server = serialize($this->server);
        }
    }
    
    
    
    
}
