<?php

namespace maze\commet;

use maze\commet\DriverHttpInterface;
use maze\base\Object;
use maze\jdb\Jsondb;
use maze\commet\Message;
use RC;

class DriverJson extends Object implements DriverHttpInterface {

    public $path;
    
    public $tableName;
    
    public $tableConfig = [];
    
    protected $_driver;

    public function init() {
        $this->_driver = new Jsondb(RC::getAlias($this->path));
    }
    
    public function create(){
        $result = true;
        if (!$this->_driver->exists($this->tableName)) {           
            $this->tableConfig = array_merge($this->tableConfig, [
                'commet_id' => ['auto_increment'],
                'id',
                'time'
            ]);
             
            $result = $this->_driver->create($this->tableName, $this->tableConfig);
            if (!$result) {
                throw new \Exception($this->_driver->status(true), $this->_driver->status());
            }
        }
        return $result;
    }

    public function getIs(){
        return $this->tableName !== null && $this->_driver->exists($this->tableName);
    }
    public function add($id, array $message) {
        $data = array_merge($message, ['id' => $id, 'time' => time()]);       
        return $this->getDriver()->insert($this->tableName, $data);
    }

    public function findByID($id) {
        if(!$this->getIs()) return false;
        $result = $this->getDriver()->select('*', $this->tableName, ['where' => ['id' => $id]]);
        return $result;
    }

    public function delete($id) {
        if(!$this->getIs()) return false;
        $where = ['id' => $id];       
        return $this->getDriver()->delete($this->tableName, $where);
    }


    public function getDriver() {
        return $this->_driver;
    }

}
