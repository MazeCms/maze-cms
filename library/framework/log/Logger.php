<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
 *
 * @author nick
 */

namespace maze\log;

use RC;
use maze\helpers\DataTime;
use maze\helpers\ArrayHelper;

class Logger extends \maze\base\ObjectExtended {

    /**
     * @var array - копилка сообщений 
     */
    public $messages = [];
    
    /**
     * @var int traceLevel -  глубина выводимого стека функций
     */
    public $traceLevel = 3;
    
    /**
     * @var string path - директория куда будут писаться логи
     */
    public $path = '@root/temp/log';
    
    /**
     * @var  int - maxsize  максимальный размер файла лога в мегабайтах
     */
    public $maxsize = 1;
    /**
     * @var  int - enable  включить логирование
     */
    public $enable = 1;
    
    /**
     * @var array filter -  параметры фильтрация логов 
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     * RC::getLog()->andFilterWhere(['>=', 'datetime', '2015-06-21 14:42:40'])
     *             ->andFilterWhere(['like', 'message', 'Ошибка'])
     *             ->andFilterWhere(['component'=>'admin'])->load('exp');
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     */
    public $filter = [];
    
    /**
     *
     * @var array typeEnable - типы логов разрешеные для записей в файлы
     */
    public $typeEnable = ['db', 'error', 'cache', 'exp', 'request'];


    public static $messageLogs = [
        'db' => '\maze\log\MessageDB',
        'cache' => '\maze\log\MessageCache',
        'error'=>'\maze\log\MessageError',
        'exp'=>'\maze\log\MessageExpansion',
        'request'=>'\maze\log\MessageRequest'
    ];

    public function init() {
        parent::init();
       
        register_shutdown_function(function () {
            register_shutdown_function([$this, 'flush'], true);
        });

    }

    public function add($type, array $message) {
        if (isset(static::$messageLogs[$type])) {
            $message['class'] = static::$messageLogs[$type];
            $traces = [];
            $ts = debug_backtrace();
            array_pop($ts);
            $count = 0;

            foreach ($ts as $trace) {
                if (isset($trace['file'], $trace['line']) && strpos($trace['file'], PATH_LIBRARIES) !== 0) {
                    unset($trace['object'], $trace['args']);
                    $traces[] = $trace;
                    if (++$count >= $this->traceLevel) {
                        break;
                    }
                }
            }
            $message['traces'] = $traces;
            return $this->messages[$type][] = RC::createObject($message);
        }
    }
    
    public function create($type, array $message) {
  
        if (isset(static::$messageLogs[$type])) {
            $message['class'] = static::$messageLogs[$type];
            return  RC::createObject($message);
        }
        return null;
    }

    public function getFileSize($type) {
        return $this->getIsLogs($type) ? filesize($this->getPathType($type)) :  null;
    }

    public function getPathType($type) {
        return RC::getAlias($this->path . '/' . $type . '.log');
    }

    public function getIsLogs($type) {
        return file_exists($this->getPathType($type)) ? true : false;
    }

    public function clear($type) {
        if ($this->getIsLogs($type)) {
            @unlink($this->getPathType($type));
        }
        return $this->getIsLogs($type);
    }
    
    public function load($type){
        $messages = [];
        if ($this->getIsLogs($type)) {
            $contents = file_get_contents($this->getPathType($type));
            $contents =  preg_split('/\r\n/', $contents);
            foreach ($contents as $cont){
                
                $parse = json_decode($cont, true);
                if(is_array($parse)){
                  $messages[] = $this->create($type, $parse);
                }
            }
        }
        
     
        if(!empty($this->filter)){
            $messageFilter = [];
            foreach($messages as $mes){
                
                $result = true;
                 
                foreach ($this->filter as $f){
                    if(isset($f[0])){
                        if(($f[0] == '>=' ||  $f[0] == '<=' || $f[0] == '>' ||  $f[0] == '<') && isset($f[1]) && isset($f[2])){
                            if(isset($mes[$f[1]])){
                                if(empty($f[2])) continue;
                                $target =  $this->copareValue($mes[$f[1]]);
                                $current = $this->copareValue($f[2]);
                                switch ($f[0]){
                                    case '>=':
                                        $result = ($target >= $current);
                                        break;
                                    case '<=':
                                        $result = ($target <= $current);
                                        break;
                                    case '>':
                                        $result = ($target > $current);
                                        break;
                                    case '<':
                                        $result = ($target < $current);
                                        break;
                                }       
                            }
                        }
                        elseif($f[0] == 'like' && isset($f[1]) && isset($f[2])){
                            if(isset($mes[$f[1]])){
                                if(empty($f[2])) continue;
                                $result =  (mb_stripos($mes[$f[1]], $f[2]) !== false);
                            }
                        }
                    }
                    elseif (ArrayHelper::isAssociative($f)) {
                        
                        foreach($f as $k=>$v){
                            if(isset($mes[$k])){
                                if(empty($v)) continue;
                                if(is_array($v)){
                                    $result = in_array($mes[$k], $v);
                                }
                                elseif($mes[$k] != $v){
                                    $result = false;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if(!$result){
                        break;
                    }                              
                }
                
                if($result){
                    $messageFilter[] = $mes;
                } 
            }
            
            $messages = $messageFilter;
            
        }
        
        $this->filter = [];
        return $messages;
    }
    
    public function andFilterWhere(array $param){
        $this->filter[] = $param;
        return $this;
    }
    
    public function findById($id, $type) {
        $this->filter = [];
        $this->andFilterWhere(['id'=>$id]);
        $messages = $this->load($type);
        if(empty($messages)) return null;
        return end($messages);
    }


    protected function copareValue($val) {
        
        if(is_float($val)){
            $val = (float)$val;
        }elseif (DataTime::dataCreate($val)) {
            $val = DataTime::dataCreate($val)->getTimestamp();
        }

        if (!is_numeric($val)) {
            $val = strlen($val);
        }

        return $val;
    }


    public function flush() {
        
        foreach (static::$messageLogs as $type => $class) {
            if ($this->getIsLogs($type)) {
                if ($this->getFileSize($type) > $this->maxsize * pow(1024, 2)) {
                    @unlink($this->getPathType($type));
                }
            }
        }

        if(!$this->enable){
            return false;
        }

        foreach ($this->messages as $type => $mess) {
            if(!in_array($type, $this->typeEnable)){
                continue;
            }
                               
            $text = [];            
            foreach ($mess as $m){
                $m->formatMessage();
                $text[] = json_encode($m->attributes);
            }
            
            if (!empty($text)){
                if($this->getIsLogs($type)){                
                    $text = "\r\n".implode("\r\n", $text);
                }else{
                    $text = implode("\r\n", $text);
                }
                
                file_put_contents($this->getPathType($type), $text, FILE_APPEND | FILE_TEXT);
            }
            
        }
    }

}
