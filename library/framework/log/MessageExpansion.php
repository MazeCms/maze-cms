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

class MessageExpansion extends \maze\log\Message{
   
    /**
     * @var string -  название компаненты
     */
    public $component;

    /**
     * @var string - название действия
     */
    public $action;
    
     /**
     * @var string - описание дествия
     */
    public $message;
    
    /**
     * @var string - статут важности сообщений info | warning |  danger | success
     */
    public $status = 'info';


    public function getMessage(){
        
    }
    
    
    
    
}
