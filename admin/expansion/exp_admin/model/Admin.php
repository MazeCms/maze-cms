<?php

namespace exp\exp_admin\model;

use exp\exp_admin\table\Desktop;
use exp\exp_admin\table\Gadgets;
use maze\helpers\ArrayHelper;
use maze\table\InstallApp;
use RC;
use Text;

class Admin extends \maze\base\Model {

    public function getDesktop() {
        return Desktop::find()->orderBy('ordering')->all();
    }

    public function getDesktopByID($id) {
        return Desktop::find()->joinWith('gadgets')
                        ->where([Desktop::tableName() . '.id_des' => $id])
                        ->one();
    }
    
    public function setDefaultDesktop($id_des) {
        Desktop::updateAll(['defaults'=>0]);
        $desk = Desktop::findOne(['id_des'=>$id_des]);
        $desk->defaults = 1;
        return $desk->save();
    }

    public function getGadget($gadget) {
        $result = array();
        if ($gadget) {
            foreach ($gadget as $gad) {
                if (empty($gad->title)) {
                    $params = RC::getConf(array("type" => "gadget", "id" => $gad->id_gad, "name" => $gad->name));
                    $gad->title = $params->get("name");
                    unset($params);
                }
                $result[$gad->colonum][] = $gad;
            }
        }
        return $result;
    }

    public function getGadgets($id_des) {
        $result = Gadgets::find()->where(['id_des'=>$id_des])->orderBy('colonum, ordering')->all();
        $gadgets = [];
        foreach ($result as $gad) {
            if (empty($gad->title)) {
                $params = RC::getConf(["type" => "gadget", "id" => $gad->id_gad, "name" => $gad->name]);
                $gad->title = $params->get("name");
                unset($params);
            }
            $gadgets[$gad->colonum][] = $gad;
        }
        return $gadgets;
    }
    

    public function getSettinsGadget($id_gad) {
        $gadget = Gadgets::findOne($id_gad);
        if (empty($gadget)) {
            RC::getLog()->add('exp',['component'=>'admin',
                        'category'=>__METHOD__,
                        'action'=>'getSettinsGadget', 
                        'message'=>Text::_("LIB_FRAMEWORK_EXPANSION_PROR_EMPTY", array("id_gad", "Admin_Model_Admin->getSettinsGadget"))]);
            return false;
        }
        $param = RC::getConf(["type" => "gadget", "id" => $gadget->id_gad, "name" => $gadget->name], $gadget->param);
        $gadget->param = $param;
        unset($param);
        return $gadget;
    }

    public function deleteGadeget($id) {
        return Gadgets::findOne($id)->delete();
    }

    public function deleteDesktop($id) {
        $transaction = RC::getDb()->beginTransaction();
        try {
            Gadgets::deleteAll(['id_des' => $id]);
            Desktop::findOne($id)->delete();
            if($des = Desktop::find()->orderBy('ordering')->one()){
                $des->defaults = 1;
                $des->save();
            }
            
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function getInstallGadgets() {
        $result = array();
        $gadgets = InstallApp::findAll(['type' => 'gadget']);
        if (empty($gadgets)) {
             RC::getLog()->add('exp',['component'=>'admin', 
                        'category'=>__METHOD__,
                        'action'=>'getInstallGadgets', 
                        'message'=>Text::_("EXP_ADMIN_DESSKTOP_ADDGAD_ERR")]);
            return false;
        }
        foreach ($gadgets as $gad) {
            $param = RC::getConf(["type" => "gadget", "name" => $gad->name]);

            $data = new \stdClass();
            $data->sys_name = $gad->name;
            $data->name = $param->get("name");
            $data->description = $param->get("description");
            $data->copyright = $param->get("copyright");
            $data->license = $param->get("license");
            $data->version = $param->get("version");
            $data->author = $param->get("author");
            $data->email = $param->get("email");
            $data->created = $param->get("created");
            $data->siteauthor = $param->get("siteauthor");
            $result[] = $data;
            unset($param);
            unset($data);
        }
        return $result;
    }

    public function addGadgetDesktop($name, $id_des) {
             
        $gad = new Gadgets(['scenario'=>'add']);
        $gad->name = $name;
        $gad->id_des = $id_des;
        
        if(!$gad->validate()){
            return $gad;
        }   
        
        $order = Gadgets::find()->where(['id_des'=>$id_des, 'colonum'=>0])->max('ordering') + 1;
        $gad->ordering = $order + 1;
        $gad->colonum = 0;
        $gad->save();
        return $gad;
    }
    
    public function getContentGadget($gadget) {
        
        if(empty($gadget)) {
            RC::getLog()->add('exp',['component'=>'admin', 
                        'category'=>__METHOD__,
                        'action'=>'getContentGadget', 
                        'message'=>Text::_("LIB_FRAMEWORK_EXPANSION_PROR_EMPTY", array("id_gad", "Admin_Model_Admin->getGadget"))]
                    );
            return false;
        }
        
        if(is_string($gadget)){
            $gadget = Gadgets::findOne($gadget);
        }
        
        $params = RC::getConf(["type" => "gadget", "id" => $gadget->id_gad, "name" => $gadget->name], $gadget->param);  
        return RC::app()->view->renderFile('@admin/gadgets/gad_'.$gadget->name.'/gad.'.$gadget->name.'.php', ['params'=>$params, 'gadget'=>$gadget]);
    }
    
    public function orderGadgets($ordering, $colonum, $id_gad){
      $gad = Gadgets::findOne($id_gad);
      if(!$gad->id_gad) return false; 
      $gad->ordering = $ordering;
      $gad->colonum = $colonum;
      return $gad->update(false, ['ordering', 'colonum']); 
    }
    
    public function orderTabs($ordering, $id_des){
        $desk = Desktop::findOne($id_des);
        if(!$desk->id_des) return false;
        $desk->ordering = $ordering;
        return $desk->update(false, ['ordering']); 
    }

}
