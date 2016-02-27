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

    public function recover($login){
        $user = Users::find()->where(['or', ['username'=>$login], ['email'=>$login]])->one();
        if($user){
            $user->scenario = 'recover';
            $user->keyactiv = RC::app()->session->generateKey(50);
            $time = RC::app()->getComponent('user')->config->getVar('recTimeOut');
            $time = is_integer((int)$time) ? $time : 5;
            $user->timeactiv = date('Y-m-d H:i:s', time() + (60 * $time));
            if($user->save()){
                RC::getCache("fw_access")->clearTypeFull();
                return $user;
            }   
        }
        
        return false;
    }
    
    public function editPass($code, $pass){
        $user = Users::findOne(['keyactiv'=>$code]);
        if($user){
           $user->scenario = 'editpass';
           $user->keyactiv = null;
           $user->password = md5($pass);
           if($user->save()){
               RC::getCache("fw_access")->clearTypeFull();
               return true;
           }
           
        }
        return false;
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
}
