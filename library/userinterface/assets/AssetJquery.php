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

class AssetJquery extends AssetBundle{
    
    public $basePath = '@lib/jquery/core/';
    
    public $baseUrl = '/library/jquery/core/';
    
    public $js = ['jquery-1.10.1.min.js', 'jquery-migrate-1.2.1.min.js'];
    
    public $jsOptions = ['sort'=>100];
}
