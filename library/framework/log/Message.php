<?php

namespace maze\log;

use RC;
use maze\helpers\VarDumper;

abstract class Message extends \maze\base\Model {
    
    public $id;

    public $datetime;
    /**
     * @var string - метод вызова зопроса
     */
    public $category;
    
    /**
     * @var string -  ip адрес клиента
     */
    public $ip;
    
    /**
     * @var int id пользователя если авторизован 
     */
    public $user_id;
    
    /**
     * @var int - id пользовательской сессии
     */
    public $session_id;
    
    /**
     * @var array - стек вызова метода класса
     */
    public $traces;


    /**
     *  Возвращает строку сообщения
     */
    abstract public function getMessage();

    public function formatMessage() {
        if($this->id == null){
            $this->id = uniqid();
        }
        
        $this->getMessage();
        $this->getMessagePrefix();
        $this->getMessageTraces();
    }

    public function getMessageTraces() {
        $result = [];
        if (is_array($this->traces)) {
            foreach ($this->traces as $t) {
                $result[] = $t['file'].'('.$t['line'].')';
            }
        }
        $this->traces = implode(", ", $result);
        
    }

    public function getMessagePrefix() {

        $request = RC::app()->request;
        $this->ip = $request->getUserIP() ? $request->getUserIP() : '-';

        $this->user_id = RC::app()->access->get() ? RC::app()->access->getUid() : '-';

        $this->session_id = RC::app()->session->getSessionId() ? RC::app()->session->getSessionId() : '-';
        $this->datetime = date('Y-m-d H:i:s');
    }

}
