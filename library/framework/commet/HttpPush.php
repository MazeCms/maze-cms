<?php

namespace maze\commet;

use RC;
use maze\base\Message;

class HttpPush extends \maze\base\Object {

    /**
     * @var int $limit -   время в секундах жизни соединения
     */
    public $limit = 360;
    
    /**
     * @var int $time -   время старта скрипта unixstamp
     */
    public $time;
    
    /**
     * @var время задежки скрипта  в милисекундах 
     */
    public $delay = 10000;
    
    public $driver = [
        'class'=>'maze\commet\DriverJson', 
        'path'=>'@root/temp/cache/'
    ];

    /**
     * @var string $id - уникальный код текущего пользовалетя 
     */
    public $id;
    
    public $event = 'commet';

    protected $_message;
    
    protected $_instanceDriver;


    public function init() {
        $this->time = time();
        if(!$this->id){
            $this->id = session_id();
        }
    }
    
    public function add(array $message) {
        $this->instanceDriver->create();
        return $this->instanceDriver->add($this->id, $message);
    }

    public function start() {
      
        set_time_limit($this->limit*10);;
        while ((time() - $this->time) < $this->limit) {
    
            if (!empty($this->event) && $this->instanceDriver->is && ($message = $this->find())) {               
                $this->delete();
                $this->_message = $message;
                
                break;
            }
            usleep($this->delay);
        }
        
        return $this->_message;
    }
    
    public function getInstanceDriver(){
        if($this->_instanceDriver == null){
            $this->driver['tableName'] = $this->event;
            $this->_instanceDriver = RC::createObject($this->driver);
        }
        return $this->_instanceDriver;
    }


    public function find(){
        return $this->instanceDriver->findByID($this->id);
    }
    
    public function delete() {
        return $this->instanceDriver->delete($this->id);
    }

    public function getMessage(){        
        return $this->_message;
    }

}
