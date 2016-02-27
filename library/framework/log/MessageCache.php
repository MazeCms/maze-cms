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

class MessageCache extends \maze\log\Message{
   
    /**
     * @var тип запроса запись - чтение
     */
    public $type;

    /**
     * @var string -  текст запроса
     */
    public $text;
    
    /**
     * @var string -  группа
     */
    public $group;


    public function getMessage(){
        
    }
    
    
    
    
}
