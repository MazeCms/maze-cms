<?php

namespace exp\exp_elfinder\model;

use RC;
use Text;
use maze\helpers\ArrayHelper;
use exp\exp_elfinder\table\Profile as PFTable;
use exp\exp_elfinder\table\Dir;
use exp\exp_elfinder\table\Uploadallow;
use exp\exp_elfinder\table\Attributes;

class Path extends \maze\base\Model {

    
    public function save($form, $attr) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if ($form->path_id) {
                $dir = Dir::findOne($form->path_id);
                
            } else {
                $dir = new Dir();
                $dir->sort = Dir::find()->where(['profile_id'=>$form->profile_id])->count() + 1;
            }

            $dir->attributes = $form->attributes;
            
            
            if (!$dir->save()) {               
                throw new \Exception();
            }
           
            Uploadallow::deleteAll([ 'path_id' => $dir->path_id]);
            if (!empty($form->uploadallow) && is_array($form->uploadallow)) {
                foreach ($form->uploadallow as $type) {
                    $upload = new Uploadallow();
                    $upload->path_id = $dir->path_id;
                    $upload->mimetypes = $type;
                    if (!$upload->save()) {
                        throw new \Exception();
                    }
                }
            }
            
            Attributes::deleteAll([ 'path_id' => $dir->path_id]);
            if (!empty($attr) && is_array($attr)) {
                foreach ($attr as $at) {
                    $at->path_id = $dir->path_id;
                    if(!$at->validate()) continue;
                    if (!$at->save()) {
                        throw new \Exception();
                    }
                }
            }
            
            $form->path_id = $dir->path_id;

            $transaction->commit();
        } catch (\Exception $e) {
             $form->addError('path_id', Text::_('EXP_ELFINDER_PROFILE_SAVEERROR'));
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function deleteItems($id_menu) {
//        $menu = \maze\table\Menu::find()->where(['id_menu' => $id_menu])->one();
//        if (!$menu)
//            return false;
//
//        $parents = $this->getParnetsItem($menu->id_group);
//        $id = [];
//
//        foreach ($id_menu as $id_m) {
//            $id = array_merge($id, $this->getChildId($parents, $id_m));
//            $id[] = $id_m;
//        }
//        \maze\table\Menu::deleteAll(['id_menu' => $id]);
//        return $id;
    }

   

}
