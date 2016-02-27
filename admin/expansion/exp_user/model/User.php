<?php

namespace exp\exp_user\model;

use maze\helpers\ArrayHelper;
use maze\table\Users;
use maze\table\Roles;
use maze\table\UserRoles;
use maze\table\RolePrivate;
use maze\table\Privates;
use RC;

class User extends \maze\base\Model {

    public function bloc($id_user, $val) {
        $id = \RC::app()->access->getUid();

        if (in_array($id, $id_user)) {
            $key = array_search($id, $id_user);
            unset($id_user[$key]);
        }

        if (empty($id_user))
            return false;

        Users::updateAll(['bloc' => $val], ['id_user' => $id_user]);
        RC::getCache("fw_access")->clearTypeFull();
        return true;
    }

    public function saveUser($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                 
                throw new \Exception();
            }

            if ($form->id_user) {
                $user = Users::findOne($form->id_user);
            } else {
                $user = new Users();
            }
            
            $user->scenario = 'save';
            if ($form->new_password) {
                $user->password = md5($form->new_password);
            }
            $user->setAttributes($form->attributes);
            
            if (!$user->save()) {
               
                $form->addError('id_user', \Text::_('EXP_USER_CONTROLLER_MESS_SAVE_ERROR'));
                throw new \Exception();
            }
            UserRoles::deleteAll(['id_user' => $user->id_user]);
            if ($form->id_role) {
                foreach ($form->id_role as $id) {
                    $role = new UserRoles();
                    $role->id_user = $user->id_user;
                    $role->id_role = $id;
                    if (!$role->validate() || !$role->save()) {
                        $form->addError('id_role', \Text::_('EXP_USER_CONTROLLER_MESS_SAVE_ERROR'));
                        throw new \Exception();
                    }
                }
            }
            $form->id_user = $user->id_user;
            $transaction->commit();
            RC::getCache("fw_access")->clearTypeFull();
        } catch (\Exception $e) {
            
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function pack($form) {
        $users = $form->id_user;
        foreach ($users as $id) {
            $user = Users::findOne($id);
            if (!$user)
                continue;
            $form->scenario = 'role';
            $form->id_user = $id;
            
            if ($form->validate()) {
                
                $user->id_lang = $form->id_lang;
                $user->timezone = $form->timezone;
                $user->editor_admin = $form->editor_admin;
                $user->editor_site = $form->editor_site;
                $user->scenario = 'save';
                if (!$user->save()) {
                    $form->addError('id_user', \Text::_('EXP_USER_CONTROLLER_MESS_PACKNOHANDLER_WAR'));
                }
                UserRoles::deleteAll(['id_user' => $user->id_user]);
                if ($form->id_role) {
                    foreach ($form->id_role as $idr) {
                        $role = new UserRoles();
                        $role->id_user = $user->id_user;
                        $role->id_role = $idr;
                        if (!$role->validate() || !$role->save()) {
                            $form->addError('id_role', \Text::_('EXP_USER_CONTROLLER_MESS_SAVE_ERROR'));
                        }
                    }
                }
            }
        }
        RC::getCache("fw_access")->clearTypeFull();
    }
    
    public function delete($id_user)
    {
        $users = Users::findAll(['id_user'=>$id_user]);
        if(!$users) return false;
        foreach($users as $user)
        {
            if($user->id_user != \RC::app()->access->getUid())
            {
               $user->delete(); 
            }
        }
        RC::getCache("fw_access")->clearTypeFull();
        return true;
    }
    
    
    public function saveRole($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                throw new \Exception();
            }

            if ($form->id_role) {
                $role = Roles::findOne($form->id_role);
                if(!$role)
                {
                    $form->addError('id_role', \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR'));
                    throw new \Exception();
                }
            } else {
                $role = new Roles();
            }
 
            $role->attributes = $form->attributes;
            if (!$role->save()) {
                $form->addError('id_role', \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR'));
                throw new \Exception();
            }
            
            RolePrivate::deleteAll(['id_role' => $role->id_role]);
            $privRoot = Privates::find()->where(['exp_name' => 'system', 'name' => 'ROOT'])->one()->id_priv;
            if(is_array($form->private) && in_array($privRoot, $form->private)){
               RolePrivate::deleteAll(['id_priv' => $privRoot]); 
            } 
            if (is_array($form->private) && $form->private) {
                foreach ($form->private as $id) {
                    $rolePriv = new RolePrivate();
                    $rolePriv->id_role = $role->id_role;
                    $rolePriv->id_priv = $id;
                    if (!$rolePriv->validate() || !$rolePriv->save()) {
                        $form->addError('private', \Text::_('EXP_USER_ROLE_CONTROLLER_SAVE_ERR'));
                        throw new \Exception();
                    }
                }
            }
            $form->id_role = $role->id_role;
            $transaction->commit();
            RC::getCache("fw_access")->clearTypeFull();
        } catch (\Exception $e) {
            $transaction->rollBack();
            \RC::app()->setError($e);
            return false;
        }
        return true;
    }


}
