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

class AssetiCheck extends AssetBundle{
    
    public $basePath = '@lib/jquery/checkbox/master/';
    
    public $baseUrl = '/library/jquery/checkbox/master/';
    
    public $js = ['jquery.icheck.min.js'];
    
    public $css = ['skins/all.css'];

}
