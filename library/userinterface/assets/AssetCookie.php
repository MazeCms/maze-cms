<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssetChosen
 *
 * @author Николай Константинович Бугаёв http://maze-studio.ru
 */
namespace ui\assets;
use maze\document\AssetBundle;

class AssetCookie extends AssetBundle{
    
    public $basePath = '@lib/jquery/cookie';
    
    public $baseUrl = '/library/jquery/cookie';
    
    public $js = ['jquery.cookie.js'];
        
    public $depends = ['ui\assets\AssetJquery'];
  
}
