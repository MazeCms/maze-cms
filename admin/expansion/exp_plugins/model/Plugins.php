<?php

namespace exp\exp_plugins\model;

use maze\helpers\ArrayHelper;
use maze\table\AccessRole;

class Plugins extends \maze\base\Model {

    public function getGroupName(){
        
        return ArrayHelper::map(\maze\table\Plugin::find()->distinct('group_name')->asArray()->all(), 'group_name', 'group_name');
    }
    
    public function enable($id_plg, $enable){
         \maze\table\Plugin::updateAll(['enabled' => $enable], ['id_plg' => $id_plg]);
    }
    
    public function savePlugin($form){
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                throw new \Exception();
            }
            
            $plg = \maze\table\Plugin::findOne($form->id_plg);
           
            if(!$plg){
                $form->addError('id_plg', \Text::_('EXP_PLUGINS_CONTROLLER_MESS_SAVE_ERROR'));
                throw new \Exception();
            }
            
            $plg->attributes = $form->attributes;

            if (!$plg->save()) {
                $form->addError('id_plg', \Text::_('EXP_PLUGINS_CONTROLLER_MESS_SAVE_ERROR'));
                throw new \Exception();
            }

            AccessRole::deleteAll(['exp_name' => 'plugins', 'key_role' => 'plugin', 'key_id' => $plg->id_plg]);
            if (!empty($form->id_role) && is_array($form->id_role)) {
                foreach ($form->id_role as $id_role) {
                    $role = new AccessRole();
                    $role->exp_name = 'plugins';
                    $role->key_role = 'plugin';
                    $role->key_id = $plg->id_plg;
                    $role->id_role = $id_role;
                    if (!$role->save()) {
                        $form->addError('id_role', \Text::_('EXP_PLUGINS_CONTROLLER_MESS_SAVE_ERROR'));
                        throw new \Exception();
                    }
                }
            }
           
            $form->id_plg = $plg->id_plg;
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
    
}
