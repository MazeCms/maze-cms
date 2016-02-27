<?php

namespace maze\install;

use Text;

class BaseInstall extends \maze\base\Object
{
    /**
     * @var string - текущий тип объекта
     */
    protected  $type;
    
    /**
     * @var string - имя расширения
     */
    protected $name;
    
    /**
     * @var int - принадлежность расширения
     */
    protected $front;

    /**
     * @var array - доступные языки расширения
     */
    protected $lang;
    
    /**
     * @var array - языки расширения по умолчанию
     */
    protected $defaultLang;


    /**
     * @var array - массив ошибок
     */
    protected $_errors = [];
    
    /**
     * @var string -  путь к директории инсталяции или деинсталяции 
     */
    protected $path;


    /**
     * команды уставноки
     * 
     * @return array - массив команд вида ['action'=>'Название процесса']
     */
    public function getCommands(){
        return [];
    }
    
    public function getName(){
        return $this->name;
    }
    
    public function getType(){
        return $this->type;
    }
    
    public function getFront(){
        return $this->front;
    }

    public function getLang(){
        return $this->lang;
    }
    

    public function getPath(){
        return $this->path;
    }
    
    public function setPath($path){
        
        $this->path = $path;
        
    }
    
    public function hasErrors($cmd = null)
    {
        return $cmd === null ? !empty($this->_errors) : isset($this->_errors[$cmd]);
    }
    
    public function getErrors($cmd = null)
    {
        if ($cmd === null) {
            return $this->_errors === null ? [] : $this->_errors;
        } else {
            return isset($this->_errors[$cmd]) ? $this->_errors[$cmd] : [];
        }
    }
    
    public function addError($cmd, $error = '', $prop = [])
    {
        $this->_errors[$cmd][] = $this->text($error, $prop);
        
        return $this;
    }
    
    public function clearErrors($cmd = null)
    {
        if ($cmd === null) {
            $this->_errors = [];
        } else {
            unset($this->_errors[$cmd]);
        }
    }
    
    public function hasCommand($cmd){
        $commands = $this->getCommands();
        if(isset($commands[$cmd])){
            return $this->hasMethod('action'.$cmd);
        }
        return false;
    }
    
    public function text($const, $prop = []){
        return Text::_($const, $prop);
    }
    
    public function exec($cmd){
        
       if($this->hasCommand($cmd)){
           return call_user_func([$this, 'action'.$cmd]);
       }else{
           $this->addError($cmd, 'Данного процесса уставновки не существует');
       }
       
       return $this;
       
    }

}
