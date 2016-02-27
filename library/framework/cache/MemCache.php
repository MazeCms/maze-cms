<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MemCache
 *
 * @author nick
 */

namespace maze\cache;

use maze\helpers\FileHelper;
use RC;


class MemCache extends \maze\cache\Cache {
    
    /**
     * @var boolean whether to use memcached or memcache as the underlying caching extension.
     * If true, [memcached](http://pecl.php.net/package/memcached) will be used.
     * If false, [memcache](http://pecl.php.net/package/memcache) will be used.
     * Defaults to false.
     */
    public $useMemcached = true;
 
    /**
     * @var array options for Memcached. This property is used only when [[useMemcached]] is true.
     * @see http://ca2.php.net/manual/en/memcached.setoptions.php
     */
    public $options;
    /**
     * @var string memcached sasl username. This property is used only when [[useMemcached]] is true.
     * @see http://php.net/manual/en/memcached.setsaslauthdata.php
     */
    public $username;
    /**
     * @var string memcached sasl password. This property is used only when [[useMemcached]] is true.
     * @see http://php.net/manual/en/memcached.setsaslauthdata.php
     */
    public $password;

    /**
     * @var \Memcache|\Memcached the Memcache instance
     */
    private $_cache = null;
    /**
     * @var array list of memcache server configurations
     */
    private $_servers = [];
    
    public $path = '@root/temp/cache';
    
    public function init()
    {
       
        if (!is_dir(RC::getAlias($this->path.'/'.$this->type))) {
            FileHelper::createDirectory(RC::getAlias($this->path.'/'.$this->type));
        }
        
        $this->addServers($this->getMemcache(), $this->getServers());
    }
    
    /**
     * @param \Memcache|\Memcached $cache
     * @param array $servers
     * @throws InvalidConfigException
     */
    protected function addServers($cache, $servers)
    {
        
        if (empty($servers)) {
            $servers = [new MemCacheServer([
                'host' => '127.0.0.1',
                'port' => 11211,
            ])];
        } else {
            foreach ($servers as $server) {
                if ($server->host === null) {
                    throw new \Exception("The 'host' property must be specified for every memcache server.");
                }
            }
        }
       
        if ($this->useMemcached) {
            $this->addMemcachedServers($cache, $servers);
        } else {
            $this->addMemcacheServers($cache, $servers);
        }
    }

    /**
     * @param \Memcached $cache
     * @param array $servers
     */
    protected function addMemcachedServers($cache, $servers)
    {
        $existingServers = [];
        if ($this->type !== null) {
            foreach ($cache->getServerList() as $s) {
                $existingServers[$s['host'] . ':' . $s['port']] = true;
            }
        }
        foreach ($servers as $server) {
            if (empty($existingServers) || !isset($existingServers[$server->host . ':' . $server->port])) {
                $cache->addServer($server->host, $server->port, $server->weight);
            }
        }
    }

    /**
     * @param \Memcache $cache
     * @param array $servers
     */
    protected function addMemcacheServers($cache, $servers)
    {
        $class = new \ReflectionClass($cache);
        $paramCount = $class->getMethod('addServer')->getNumberOfParameters();
        foreach ($servers as $server) {
            // $timeout is used for memcache versions that do not have $timeoutms parameter
            $timeout = (int) ($server->timeout / 1000) + (($server->timeout % 1000 > 0) ? 1 : 0);
            if ($paramCount === 9) {
                $cache->addServer(
                    $server->host,
                    $server->port,
                    $server->persistent,
                    $server->weight,
                    $timeout,
                    $server->retryInterval,
                    $server->status,
                    $server->failureCallback,
                    $server->timeout
                );
            } else {
                $cache->addServer(
                    $server->host,
                    $server->port,
                    $server->persistent,
                    $server->weight,
                    $timeout,
                    $server->retryInterval,
                    $server->status,
                    $server->failureCallback
                );
            }
        }
    }
    
    /**
     * Returns the underlying memcache (or memcached) object.
     * @return \Memcache|\Memcached the memcache (or memcached) object used by this cache component.
     * @throws InvalidConfigException if memcache or memcached extension is not loaded
     */
    public function getMemcache()
    {
        if ($this->_cache === null) {
            $extension = $this->useMemcached ? 'memcached' : 'memcache';
            if (!extension_loaded($extension)) {
                throw new \Exception("MemCache requires PHP $extension extension to be loaded.");
            }

            if ($this->useMemcached) {
                $this->_cache = $this->type !== null ? new \Memcached($this->type) : new \Memcached;
                
                if ($this->username !== null || $this->password !== null) {
                    $this->_cache->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
                    $this->_cache->setSaslAuthData($this->username, $this->password);
                }
                if (!empty($this->options)) {
                    $this->_cache->setOptions($this->options);
                }
            } else {
                $this->_cache = new \Memcache;
            }
        }
       

        return $this->_cache;
    }

    /**
     * Returns the memcache or memcached server configurations.
     * @return MemCacheServer[] list of memcache server configurations.
     */
    public function getServers()
    {
        return $this->_servers;
    }

    /**
     * @param array $config list of memcache or memcached server configurations. Each element must be an array
     * with the following keys: host, port, persistent, weight, timeout, retryInterval, status.
     * @see http://php.net/manual/en/memcache.addserver.php
     * @see http://php.net/manual/en/memcached.addserver.php
     */
    public function setServers($config)
    {
        foreach ($config as $c) {
            $this->_servers[] = new MemCacheServer($c);
        }
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string $key a unique key identifying the cached value
     * @return string|boolean the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key)
    {
        return $this->_cache->get($key);
    }

    protected function setValue($key, $value)
    {
        $expire = $this->time > 0 ? intval($this->time) + time() : 0;

        return $this->useMemcached ? $this->_cache->set($key, $value, $expire) : $this->_cache->set($key, $value, 0, $expire);
    }
    
    protected function deleteValue($key)
    {
        return $this->_cache->delete($key, 0);
    }
    
    protected function deleteType() {
        
        return $this->_cache->flush();
    }
    
    public function fullClear(){

        $dir = scandir(RC::getAlias($this->path));
        if($dir){
            foreach($dir as $d){
                if($d !== '..' && $d !== '.'){
                    RC::getCache($d)->deleteType();
                    if(is_dir(RC::getAlias($this->path.'/'.$d))){
                         FileHelper::removeDirectory(RC::getAlias($this->path.'/'.$d));
                    }
                   
                }
            }
        }
        
    }
}
