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

class AssetUpload extends AssetBundle{
    
    public $basePath = '@lib/jquery/ajaxupload';
    
    public $baseUrl = '/library/jquery/ajaxupload';
    
    public $js = ['jquery.ui.widget.js', 'jquery.iframe-transport.js', 'jquery.fileupload.js'];
  
}
