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

class AssetChosen extends AssetBundle{
    
    public $basePath = '@lib/jquery/select/chosen';
    
    public $baseUrl = '/library/jquery/select/chosen';
    
    public $js = ['chosen.jquery.js'];
    
    public $css = ['chosen.css'];
}
