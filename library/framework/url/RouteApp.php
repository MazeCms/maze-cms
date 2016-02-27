<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RouteApp
 *
 * @author nick
 */
namespace maze\url;

use maze\base\Object;
use URI;

abstract class RouteApp extends Object{
    
    /**
     * @var SiteRouter router - экземпляр класса 
     */
    public $router;
    
    /**
     * @var string path - текущий маршрут
     */
    public $path;
    
    abstract function parseRoute();
    
    abstract function createRoute($path, $params);
    
    
}
