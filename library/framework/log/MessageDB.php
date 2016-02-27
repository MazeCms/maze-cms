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

class MessageDB extends \maze\log\Message{
   
    /**
     * @var int - время затраченное на исполнение запроса
     */
    public $time;

    /**
     * @var string -  текст запроса
     */
    public $query;
    

    public function beginQuery(){
        $this->time = microtime(true);
    }
    
    public function endQuery(){
        $end = microtime(true);
        $this->time = $end - $this->time;
    }


    
    public function getMessage(){
        
    }
    
    
    
    
}
