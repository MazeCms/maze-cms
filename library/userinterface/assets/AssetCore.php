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

class AssetCore extends AssetBundle {

    public $basePath = '@lib/jquery/cms';
    public $baseUrl = '';
    public $js = ['jquery.admin.js'];
    public $depends = [
        'ui\assets\AssetJquery',
        'ui\assets\AssetJqueryUI',
        ['ui\assets\AssetBootstrap', ['js' => []]],
        'ui\assets\AssetFontAwesome',
        'ui\assets\AssetLoad',
        'ui\assets\AssetDialog',
        'ui\assets\AssetPopover',
        'ui\assets\AssetMenu',
        'ui\assets\AssetScrollbar',
        'ui\assets\AssetCookie',
        'ui\assets\AssetJson',
        'ui\assets\AssetStyleCore'
    ];
    public $jsOptions = ['sort' => 93];
    public $cssOptions = ['sort' => 93];

}
