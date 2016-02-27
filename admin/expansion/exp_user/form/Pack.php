<?php

namespace exp\exp_user\form;

use maze\base\Model;

class Pack extends Model {
    
    public $id_user;
    
    public $id_role;
    
    public $id_lang;
        
    public $timezone;
    
    public $editor_admin;
    
    public $editor_site;
        
    public function rules() {
        return [
            [["id_user", "id_role"], "required"],
            [['id_lang'], 'default', 'value'=>0],
            ['id_role', 'validRoles', 'on'=>'role'],
            [['editor_admin', 'editor_site', 'timezone'], 'string']

        ];
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
            "id_lang"=>\Text::_("EXP_USER_FORM_LABEL_LANG"),
            "id_role"=>\Text::_("EXP_USER_FORM_LABEL_ROLE"),
            "timezone"=>\Text::_("EXP_USER_FORM_LABEL_META_TIMEZONE"),
            "editor_admin"=>\Text::_("EXP_USER_FORM_LABEL_META_EDITORADMIN"),
            "editor_site"=>\Text::_("EXP_USER_FORM_LABEL_META_EDITORSITE")
        ];
    }

}
