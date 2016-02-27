<?php

defined('_CHECK_') or die("Access denied");

use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use exp\exp_admin\table\Desktop;
use exp\exp_admin\table\Gadgets;

class Admin_Controller extends Controller {

    public function accessFilter() {
        return [     
            'addDesktopFrom editDesktopFrom' => ["admin", "ADD_DESKTOP"], 
            'deleteDesktop'=>['admin','DELETE_DESKTOP'],
            'addGadget sortGadgets'=>['admin','ADD_GADGET'],
            'settingsGadget'=>['admin', 'SETTINGS_GADGET'],
            'deleteGadget'=>['admin', 'DELETE_GADGET']
        ];
    }
    
    public function actionDisplay() {

        $model = $this->model("Admin");
        $desktop = $model->getDesktop();
        $gadgets = null;
        $marking = null;
        $id_des = null;
        if ($desktop) {
            foreach ($desktop as $des) {
                if ($des->defaults == 1) {
                    $marking = $des->param;
                    $id_des = $des->id_des;
                    $gadgets = $model->getGadgets($des->id_des);
                }
            }
        }

        return parent::display([
                    'desktop' => $desktop,
                    'gadgets' => $gadgets,
                    'model' => $model,
                    'marking' => $marking,
                    'id_des' => $id_des
        ]);
    }

    public function actionDefaultsDesktop($id) {
        if ($id) {
            $this->model("Admin")->setDefaultDesktop($id);
        }
        return $this->setRedirect(['/admin/admin']);
    }

    public function actionAddDesktopFrom() {
        $modelForm = $this->form('Desktop');


        if ($this->request->isAjax() && $this->request->isPost() && $this->request->get('checkform')) {
            $modelForm->load($this->request->post());
            return json_encode(['errors' => FormBuilder::validate($modelForm)]);
        }

        if (!$this->request->isAjax() && !$this->request->isPost()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        if ($this->request->isPost() && $this->request->post('Desktop')) {
            $modelForm->load($this->request->post());

            $desktop = new Desktop;

            if ($modelForm->validate()) {
                $desktop->setAttributes($modelForm->getAttributes());

                if ($desktop->defaults) {
                    Desktop::updateAll(['defaults' => 0]);
                }else{
                   if(!Desktop::find()->exists()){
                       $desktop->defaults = 1;
                   }
                }

                if (!$desktop->save()) {
                    $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_SAVE_ERR'), 'error');
                    RC::getLog()->add('exp', ['component' => 'admin',
                        'category' => __METHOD__,
                        'action' => 'GetAddDesktopFrom',
                        'message' => Text::_("EXP_ADMIN_DESSKTOP_SAVE_ERR")]);
                }
                $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_SAVE_ADD", ['name' => $desktop->title]), 'success');

                return $this->setRedirect(['/admin/admin']);
            }
        }
        return $this->renderPart("formadddesktop", false, "form", ['modelForm' => $modelForm]);
    }

    public function actionEditDesktopFrom($id_des) {
        $desktop = Desktop::findOne($id_des);
        $modelForm = $this->form('Desktop');
        if (!$desktop) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_ADMIN_DESSKTOP_NOTID", ['id' => $id_des]));
        }

        if (!$this->request->isAjax() && !$this->request->isPost()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }

        $modelForm->setAttributes($desktop->getAttributes());
        $modelForm->colonum = $desktop->param['colonum'];
        $modelForm->width = $desktop->param['width'];

        if ($this->request->isAjax() && $this->request->isPost() && $this->request->get('checkform')) {
            $modelForm->load($this->request->post());
            return json_encode(['errors' => FormBuilder::validate($modelForm)]);
        }

        if ($this->request->isPost() && $this->request->post('Desktop')) {

            $modelForm->load($this->request->post());

            if ($modelForm->validate()) {
                $desktop->setAttributes($modelForm->getAttributes());
                if ($desktop->defaults) {
                    Desktop::updateAll(['defaults' => 0]);
                }
                
                Gadgets::updateAll(['colonum'=>$modelForm->colonum-1], 'colonum>:col AND id_des=:id', [':id'=>$id_des, ':col'=> $modelForm->colonum-1]);
                
                if (!$desktop->save()) {
                    $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_SAVE_ERR'), 'error');
                    RC::getLog()->add('exp', [
                        'component' => 'admin',
                        'category' => __METHOD__,
                        'action' => 'GetAddDesktopFrom',
                        'message' => Text::_("EXP_ADMIN_DESSKTOP_SAVE_ERR")]);
                }
                $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_SAVE_UPDATE", ['name' => $desktop->title]), 'success');

                return $this->setRedirect(['/admin/admin']);
            }
        }
        
        return $this->renderPart("formadddesktop", false, "form", ['modelForm' => $modelForm]);
    }

    public function actionDeleteDesktop($id_des) {

        if($this->model('Admin')->deleteDesktop($id_des)){
            $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_DELETE_OK"), 'success');
        }else{
            $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_DELETE_ERR'), 'error');
        }
        return $this->setRedirect(['/admin/admin']);
    }

//    public function actionOrderTabs() {
//        if (!$this->request->getPost("order"))
//            return false;
//        $model = $this->model('Admin');
//        foreach ($this->request->getPost("order") as $order) {
//            $model->orderTabs($order['ordering'], $order['id_des']);
//        }
//    }

    public function actionSortGadgets(array $sort) {
        if (!$this->request->isAjax()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        $model = $this->model('Admin');
        foreach ($sort as $order) {
            $model->orderGadgets($order['ordering'], $order['colonum'], $order['id_gad']);
        }
    }

    public function actionDeleteGadget($id_gad) {
        
        if (!$this->request->isAjax()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        if($this->model('Admin')->deleteGadeget($id_gad)){
            $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_GAD_DELETE_OK"), 'success');
        }else{
            $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_DELETE_ERR'), 'error');
        }
    }

    public function actionSettingsGadget($id_gad) {

        $modelForm = $this->form('FormSetiingsGadget');
        $model = $this->model("Admin");
        
        $gadget = $model->getSettinsGadget($id_gad);
        
         if (!$gadget) {
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_ADMIN_DESSKTOP_GAD_NOTID", ['id' => $id_gad]));
        }
        
        if (!$this->request->isAjax() && !$this->request->isPost()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("LIB_FRAMEWORK_CONTROLLER_REQUEST_ERR"));
        }
        
        if ($this->request->isAjax() && $this->request->isPost() && $this->request->get('checkform')) {
            $modelForm->load($this->request->getPost());
            return json_encode(['errors' => FormBuilder::validate($modelForm)]);
        }
       
            
        if ($this->request->isPost() && $this->request->post('FormSetiingsGadget')) {

            $modelForm->load($this->request->post());

            if ($modelForm->validate()) {
                $gadgets = Gadgets::findOne($id_gad);
                $gadgets->setAttributes($modelForm->getAttributes(), false);
                if ($gadgets->save(false, ['title', 'param'])) {
                    $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_GAD_UPDATE_OK", ['name' => $gadgets->title]), 'success');
                }else{
                    $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_SAVE_ERR'), 'error');
                }
                
            }
            return $this->setRedirect(['/admin/admin']);
        }
        
        
        $modelForm->id_gad = $gadget->id_gad;
        $modelForm->title = $gadget->title;

        return $this->renderPart("settings", false, "form", ['modelForm'=>$modelForm, 'gadget'=>$gadget]);
    }

    public function actionAddGadget($id_des, $name = null) {
        $model = $this->model('Admin');
        if ($name && $id_des) {
            if ($model->addGadgetDesktop($name, $id_des)) {
                $this->setMessage(Text::_("EXP_ADMIN_DESSKTOP_GAD_ADD_OK"), 'success');
            } else {
                $this->setMessage(Text::_('EXP_ADMIN_DESSKTOP_GAD_ADD_ERR'), 'error');
            }
            return $this->setRedirect(['/admin/admin']);
        }

        $gadgets = $this->model('Admin')->getInstallGadgets();

        if (!$gadgets) {
            return false;
        }


        return $this->renderPart("addgadget", false, "form", ["gadgets" => $gadgets, 'id_des' => $id_des]);
    }

    

}

?>