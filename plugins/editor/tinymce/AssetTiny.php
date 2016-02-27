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
namespace plg\editor\tinymce;

use maze\document\AssetBundle;

class AssetTiny extends AssetBundle{
    
    public $basePath = '@plg/editor/tinymce';
    
    public $baseUrl = '/plugins/editor/tinymce';
    
    public $js = ['/js/tiny_mce.js', '/js/function.js'];
    
}
