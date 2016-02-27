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

class MessageError extends \maze\log\Message{
   
    /**
     * @var string file - файл в котором произошла ошибка
     */
    public $file;

    /**
     * @var int line -  номер строки в которой произошла ошибка
     */
    public $line;
    
    /**
     * @var string message -   сообщение об ошибке
     */
    public $message;
    
    /**
     * @var int code -  код сообщение об ошибке
     */
    public $code;

    
    public function getMessage(){
       
    }
    
    
    
    
}
