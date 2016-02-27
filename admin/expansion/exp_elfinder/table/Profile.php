<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace exp\exp_elfinder\table;

use maze\db\ActiveRecord;
use maze\table\Roles;
use exp\exp_elfinder\table\Role;
use exp\exp_elfinder\table\Dir;
use maze\db\Expression;
use maze\helpers\ArrayHelper;

/**
 * Description of Category
 *
 * @author Nikolas-link
 */
class Profile extends ActiveRecord {
   
    public static function tableName()
    {       
        return '{{%elfinder_profile}}';
    }
    
    public function rules() {
        return [
            [["title", "toolbar", "requestType", "validName", "showFiles", "showFiles", "notifyDelay"], "required"],
            [['rememberLastDir', 'useBrowserHistory', 'resizable', 'enabled'], 'boolean'],
            ['notifyDelay', 'number', 'min'=>100, 'max'=> 10000],
            ['loadTmbs', 'number', 'min'=>1, 'max'=> 10000],
            ['showFiles', 'number', 'min'=>1, 'max'=> 10000], 
            ['requestType', 'in', 'range'=>['get', 'post']], 
            ['title', 'string', 'max'=>100],
            ['cssClass', 'string', 'max'=>100],
            [['commands', 'toolbar', 'navbar', 'cwd', 'files', 'ui'], 'safe']
        ];
    }
    
    public function getRole(){
        return $this->hasMany(Role::className(), ['profile_id' => 'profile_id'])->from(['er'=>Role::tableName()]);
    }
    
    public function getDir(){
        return $this->hasMany(Dir::className(), ['profile_id' => 'profile_id'])
                ->from(['dir'=>Dir::tableName()])
                ->orderBy('dir.sort');
    }

    
    public function getRoles(){
        return $this->hasMany(Roles::className(), ['id_role' => 'id_role'])
            ->viaTable(Role::tableName(), ['profile_id' => 'profile_id'])
            ->from(["r" => Roles::tableName()]);   
    }
    
    public function beforeSave($insert) {

        if (!empty($this->commands) && is_array($this->commands)) {
            $this->commands = json_encode($this->commands);
           
        }
        
        if (!empty($this->toolbar) && is_array($this->toolbar)) {
            $this->toolbar = json_encode($this->toolbar);
           
        }
        
        if (!empty($this->navbar) && is_array($this->navbar)) {
            $this->navbar = json_encode($this->navbar);
           
        }
        
        if (!empty($this->cwd) && is_array($this->cwd)) {
            $this->cwd = json_encode($this->cwd);
           
        }
        
        if (!empty($this->files) && is_array($this->files)) {
            $this->files = json_encode($this->files);
           
        }
        
        if (!empty($this->ui) && is_array($this->ui)) {
            $this->ui = json_encode($this->ui);
           
        }
        
        if($this->isNewRecord)
        {
            $this->createDate = new Expression("NOW()");
        }
        
        return true;
    }
    
    public function afterFind() {
        
        if (!empty($this->commands)) {
            $this->commands = json_decode($this->commands, true);
           
        }
        
        if (!empty($this->toolbar)) {
            $this->toolbar = json_decode($this->toolbar, true);
           
        }
        
        if (!empty($this->navbar)) {
            $this->navbar = json_decode($this->navbar, true);
           
        }
        
        if (!empty($this->cwd)) {
            $this->cwd = json_decode($this->cwd, true);
           
        }
        
        if (!empty($this->files)) {
            $this->files = json_decode($this->files, true);
           
        }
        
        if (!empty($this->ui)) {
            $this->ui = json_decode($this->ui, true);
           
        }
    }


    
}
