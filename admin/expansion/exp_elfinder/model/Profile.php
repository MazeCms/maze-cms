<?php

namespace exp\exp_elfinder\model;

use RC;
use Text;
use maze\helpers\ArrayHelper;
use exp\exp_elfinder\table\Profile as PFTable;
use exp\exp_elfinder\table\Role;

class Profile extends \maze\base\Model {

    public function getListUI() {
        return [
            'toolbar' => Text::_('EXP_ELFINDER_SETTING_TITLE_UI_TOOLBAR'),
            'places' => Text::_('EXP_ELFINDER_SETTING_TITLE_UI_PLACES'),
            'tree' => Text::_('EXP_ELFINDER_SETTING_TITLE_UI_TREE'),
            'path' => Text::_('EXP_ELFINDER_SETTING_TITLE_UI_PATH'),
            'stat' => Text::_('EXP_ELFINDER_SETTING_TITLE_UI_STAT')
        ];
    }

    public function getListNavbar() {
        return [
            'open' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_OPEN'),
            'copy' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_COPY'),
            'cut' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_CUT'),
            'paste' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_PASTE'),
            'duplicate' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_DUPLICATE'),
            'rm' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RM'),
            'info' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_INFO')
        ];
    }

    public function getListCwd() {
        return [
            'reload' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RELOAD'),
            'back' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_BACK'),
            'upload' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_UPLOAD'),
            'mkdir' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_MKDIR'),
            'mkfile' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_MKFILE'),
            'paste' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_PASTE'),
            'info' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_INFO')
        ];
    }

    public function getListFiles() {
        return [
            'getfile' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_GETFILE'),
            'open' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_OPEN'),
            'quicklook' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_QUICKLOOK'),
            'download' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_DOWNLOAD'),
            'copy' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_COPY'),
            'cut' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_CUT'),
            'paste' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_PASTE'),
            'duplicate' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_DUPLICATE'),
            'rm' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RM'),
            'edit' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT'),
            'rename' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RENAME'),
            'resize' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RESIZE'),
            'archive' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_ARCHIVE'),
            'extract' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_EXTRACT'),
            'info' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_INFO')
        ];
    }
    
    public function getListCommand(){
        return [
            'open' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_OPEN'),
            'home'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_HOME'),
            'reload' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RELOAD'),
            'up'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_UP'),
            'forward'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_FORWARD'),
            'back' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_BACK'),
            'getfile' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_GETFILE'),
            'copy' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_COPY'),
            'cut' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_CUT'),
            'rm' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RM'),
            'edit' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_EDIT'),
            'rename' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RENAME'),
            'paste' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_PASTE'),
            'mkdir' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_MKDIR'),
            'mkfile' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_MKFILE'),
            'upload' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_UPLOAD'),
            'quicklook' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_QUICKLOOK'),
            'download' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_DOWNLOAD'),            
            'resize' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_RESIZE'),
            'archive' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_ARCHIVE'),
            'extract' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_EXTRACT'),
            'info' => Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_INFO'),
            'view'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_VIEW'),  
            'sort'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_SORT'),   
            'search'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_SEARCH'),
            'help'=>Text::_('EXP_ELFINDER_SETTING_TOOLBAR_BTN_HELP')
        ];
    }

    public function save($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if ($form->profile_id) {
                $profile = PFTable::findOne($form->profile_id);
                
            } else {
                $profile = new PFTable();
                $profile->sort = PFTable::find()->count() + 1;
            }

            $profile->attributes = $form->attributes;

            if (!$profile->save()) {               
                throw new \Exception();
            }

            Role::deleteAll([ 'profile_id' => $profile->profile_id]);
            if (!empty($form->id_role) && is_array($form->id_role)) {
                foreach ($form->id_role as $id_role) {
                    $role = new Role();
                    $role->profile_id = $profile->profile_id;
                    $role->id_role = $id_role;
                    if (!$role->save()) {
                        throw new \Exception();
                    }
                }
            }
            $form->profile_id = $profile->profile_id;

            $transaction->commit();
        } catch (\Exception $e) {
             $form->addError('profile_id', Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'));
            $transaction->rollBack();
            return false;
        }
        return true;
    }
    
    public function enable($id, $enable){
        PFTable::updateAll(['enabled'=>$enable], ['profile_id'=>$id]);
    }

    public function delete($id) {
       $profile = PFTable::findAll(['profile_id'=>$id]);
       foreach($profile as $pr){
           $pr->delete();
       }
    }

   

}
