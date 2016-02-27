<?php

namespace exp\exp_user\form;

use maze\base\Model;

class User extends Model {
    
    public $id_user;
    
    public $name;
    
    public $username;
    
    public $email;
    
    public $new_password;
    
    public $repeat_password;
    
    public $newpass;
    
    public $send_email = 1;
  
    public $id_lang;
    
    public $id_role;
    
    public $avatar;
    
    public $bloc;
    
    public $lastvisitDate;
    
    public $registerDate;
    
    public $timeactiv;
    
    public $timezone;
    
    public $editor_admin;
    
    public $editor_site;
        
    public function rules() {
        return [
            [["name", "username", "email", "id_role"], "required"],
            ['username', 'match', 'pattern'=>'/^[a-z]{4,80}$/i'],  
            ['username', 'unique', 'targetClass'=>'maze\table\Users', 'targetAttribute'=>'username', 'filter'=>function($query){
                if($this->id_user) $query->andFilterWhere(['not', ['id_user'=>$this->id_user]]);
            }],
            [['name'], 'string', 'min'=>4, 'max'=>100],
            ['email', 'email'],
            ['email', 'unique', 'targetClass'=>'maze\table\Users', 'targetAttribute'=>'email', 'filter'=>function($query){
                if($this->id_user) $query->andFilterWhere(['not', ['id_user'=>$this->id_user]]);
            }],
            ['new_password', 'match', 'pattern'=>'/^[a-z0-9-_.\#]{6,80}$/i', 'skipOnEmpty'=>false, 
                'message'=>\Text::_('EXP_USER_CONTROLLER_MESS_ERROR_PASS', ['pass'=>$this->new_password]), 'on'=>['create', 'newpass']],
            ['repeat_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>['create', 'newpass']],
            ['bloc', 'validBloc', 'on'=>['update', 'newpass']],
            [['newpass', 'bloc', 'send_email'], 'boolean'],
            [['bloc', 'id_lang'], 'default', 'value'=>0],
            ['id_role', 'validRoles'],
            [['editor_admin', 'editor_site', 'timezone', 'avatar'], 'safe']

        ];
    }
    
    public function validBloc($attribute, $params)
    {
        $id = \RC::app()->access->getUid();
        if($this->bloc && $this->id_user == $id)
        {
            $this->addError($attribute, \Text::_('EXP_USER_CONTROLLER_MESS_BLOC_USERSELF_ERR'));
        }
    }
    
    public function validRoles($attribute, $params)
    {
        $id = \RC::app()->access->getUid();
        if($this->id_user == $id)
        {
            $idRoot = \RC::app()->access->getIdAdminRole();
            $selfRole = \RC::app()->access->getIdRole();
            if(in_array($idRoot, $selfRole) && !in_array($idRoot, $this->id_role))
            {
               $this->addError($attribute, \Text::_('EXP_USER_CONTROLLER_MESS_PACKNOHANDLER_WAR')); 
            }            
        } 
    }
    
    public function attributeLabels() {
        return[
            "name" => \Text::_("EXP_USER_FORM_LABEL_NICK"),
            "username" => \Text::_("EXP_USER_FORM_LABEL_NAME"),
            "email"=>"E-mail",
            "new_password"=>\Text::_("EXP_USER_FORM_LABEL_PASS"),
            "repeat_password"=>\Text::_("EXP_USER_FORM_LABEL_REPEATPASS"),
            "send_email"=>\Text::_("EXP_USER_FORM_LABEL_SENDPASS"),
            "id_lang"=>\Text::_("EXP_USER_FORM_LABEL_LANG"),
            "id_role"=>\Text::_("EXP_USER_FORM_LABEL_ROLE"),
            "bloc"=>\Text::_("EXP_USER_FORM_LABEL_BLOCK"),
            "lastvisitDate"=>\Text::_("EXP_USER_FORM_LABEL_LASTDATE"),
            "registerDate"=>\Text::_("EXP_USER_FORM_LABEL_REGDATE"),
            "timeactiv"=>\Text::_("EXP_USER_FORM_LABEL_ACTIVEDATE"),
            "avatar"=>\Text::_("EXP_USER_FORM_LABEL_AVATAR"),
            "newpass"=>\Text::_("EXP_USER_FORM_TITLE_NEWPASS"),
            "timezone"=>\Text::_("EXP_USER_FORM_LABEL_META_TIMEZONE"),
            "editor_admin"=>\Text::_("EXP_USER_FORM_LABEL_META_EDITORADMIN"),
            "editor_site"=>\Text::_("EXP_USER_FORM_LABEL_META_EDITORSITE")
        ];
    }

}
