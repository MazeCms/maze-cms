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

class AssetFieldBuinder extends AssetBundle{
    
    public $basePath = '@lib/jquery/cms';
    
    public $baseUrl = '/library/jquery/cms';
    
    public $js = ['jquery.field-builder.js'];
  
}
