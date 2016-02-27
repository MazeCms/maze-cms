<?php

namespace exp\exp_elfinder\form;

use Text;
use maze\base\Model;
use maze\helpers\ArrayHelper;

class FormProfile extends Model {
    
    public $profile_id;

    public $title;
    
    public $enabled;
    
    public $cssClass;
    
    public $rememberLastDir;
    
    public $useBrowserHistory;
    
    public $resizable;
    
    public $notifyDelay;
    
    public $loadTmbs;
    
    public $showFiles;
    
    public $validName;
    
    public $requestType;
    
    public $commands;
    
    public $ui;
    
    public $toolbar;
    
    public $navbar;
    
    public $cwd;
    
    public $files;
    
    public $id_role;

    public function rules() {
        return [
            [["title", "toolbar", "requestType", "validName", "showFiles", "showFiles", "notifyDelay", "id_role", "loadTmbs", "commands"], "required"],
            [['rememberLastDir', 'useBrowserHistory', 'resizable', 'enabled'], 'boolean'],
            ['notifyDelay', 'number', 'min'=>100, 'max'=> 10000],
            ['loadTmbs', 'number', 'min'=>1, 'max'=> 10000],
            ['showFiles', 'number', 'min'=>1, 'max'=> 10000], 
            ['requestType', 'in', 'range'=>['get', 'post']], 
            ['title', 'string', 'max'=>100],
            ['cssClass', 'string', 'max'=>100],
            [['commands', 'toolbar', 'navbar', 'cwd', 'files', 'id_role', 'ui'], 'safe']
        ];
    }

   

    public function attributeLabels() {
        return[
            "title" => Text::_("EXP_ELFINDER_PROFILE_TITLE"),
            "id_role" => Text::_("EXP_ELFINDER_MODEL_PRIFILE_ROLE"),
            "enabled" => Text::_("EXP_ELFINDER_ACTIVE"),
            "rememberLastDir" => Text::_("EXP_ELFINDER_PROFILE_REMEMBERLASTDIR"),
            "cssClass" => Text::_("EXP_ELFINDER_PROFILE_CSSCLASS"),
            "useBrowserHistory" => Text::_("EXP_ELFINDER_PROFILE_USEBROWSERHISTORY"),
            "resizable"=>Text::_("EXP_ELFINDER_PROFILE_RESIZABLE"),
            "notifyDelay"=>Text::_("EXP_ELFINDER_PROFILE_NOTIFYDELAY"),
            "loadTmbs"=>Text::_("EXP_ELFINDER_PROFILE_LOADTMBS"),
            "showFiles"=>Text::_("EXP_ELFINDER_PROFILE_SHOWFILES"),
            "validName"=>Text::_("EXP_ELFINDER_PROFILE_VALIDNAME"),
            "requestType"=>Text::_("EXP_ELFINDER_PROFILE_REQUESTTYPE"),
            "commands"=>Text::_("EXP_ELFINDER_PROFILE_COMMANDS"),
            "toolbar"=>Text::_("EXP_ELFINDER_PROFILE_TOOLBAR"),
            "navbar"=>Text::_("EXP_ELFINDER_PROFILE_NAVBAR"),
            "cwd"=>Text::_("EXP_ELFINDER_PROFILE_CWD"),
            "files"=>Text::_("EXP_ELFINDER_PROFILE_FILES"),
            "ui"=>Text::_("EXP_ELFINDER_SETTING_TITLE_UI")
        ];
    }

}
        