<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;

class Menu_Controller extends Controller {

    public function accessFilter() {
        return [
            'add edit copy sort'=>["menu", 'EDIT_MENU'],
            'delete' => ["menu", "DELET_MENU"]
        ];
    }

    public function actionDisplay() {
       

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {
          
            return (new GridFormat([
                'id' => 'menugroup-grid',
                'model' => 'maze\table\MenuGroup',
                'colonum' => 'ordering',
                'colonumData' => [
                    'id' => '$data->id_group',
                    'id_group',
                    'ordering' => '"<span class=\"menu-icon-handle\"></span>"',
                    'items' => '$data->countItems',
                    'code',
                    'name' => function($data) {
                        return Html::a('', ['/admin/menu/groupmenu', ['run'=>'menu', 'id_group'=>$data->id_group]], ['class'=>'menu-open'])
                                . '<span class="items-menu-context">' . $data->name . '</span>';
                    }
                ]
            ]))->renderJson();
        }
        return parent::display();
    }

   

    public function actionSort() {
        if (!$this->request->isAjax()) {
              throw new maze\exception\NotFoundHttpException(Text::_("EXP_MENU_ITEMS_EMPTY"));
        }
        if ($sort = $this->request->post('sort')) {
            
            foreach ($sort as $item) {
                if (isset($item['id_group']) && isset($item['ordering'])) {
                    $menu = maze\table\MenuGroup::findOne($item['id_group']);
                    $menu->ordering = $item['ordering'];                
                    $menu->save();
                }
            }
        }
    }


    public function actionDelete(array $id_group) {

        $menu = maze\table\MenuGroup::findAll(['id_group' => $id_group]);

        if (!$menu)
              throw new maze\exception\NotFoundHttpException(Text::_("EXP_MENU_DELETE_ERROR"));

        foreach ($menu as $m) {
            $items = \maze\table\Menu::find()->joinWith('route')->where(['id_group'=>$m->id_group])->all();
            foreach($items as $item){
                $item->route->delete();
            }
            if ($m->delete()) {
                RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'message'=>Text::_("EXP_MENU_DELETE_ERROR")]);
            }
        }

        $this->setMessage(Text::_("EXP_MENU_CONT_DEL_MESS_YES"), 'success');

        $this->setRedirect(['/admin/menu']);
    }

    public function actionCopy(array $id_group) {
   
        if($this->model('Menu')->copyGroup($id_group))
        {
            $this->setMessage(Text::_("EXP_MENU_CONT_COPY_MESS_YES"), 'success');
        }
        else
        {           
            RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'copy', 
                        'message'=>Text::_("EXP_MENU_ADD_SAVE_MESS_NO")]);
            $this->setMessage(Text::_("EXP_MENU_ADD_SAVE_MESS_NO"), 'error');
        }

        
        $this->setRedirect(['/admin/menu']);
    }

    public function actionAdd() {

        $form = $this->form('Menu');
        $form->scenario = 'create';
            
        if ($this->request->isPost()) {
            
            $form->load($this->request->post());
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-menu') {

                return json_encode(['errors' => FormBuilder::validate($form)]);
            }

            if ($this->request->isPost() && $this->request->post('Menu')) {

                if ($form->validate()) {
                    $menu = new maze\table\MenuGroup();
                    $menu->setAttributes($form->getAttributes());
                    if ($menu->save()) {

                        $this->setMessage(Text::_("EXP_MENU_ADD_SAVE_MESS_YES"), 'success');
                        if ($this->request->get('action') == 'saveClose') {
                            return $this->setRedirect([['run' => 'display']]);
                        }
                        return $this->setRedirect([['run' => 'edit', 'id_group' => $menu->id_group]]);
                    } else {
                        $this->setMessage($menu->getErrors(), 'error');
                    }
                } else {
                    $this->setMessage($form->getErrors(), 'error');
                }
            }
        }

        return $this->renderPart("form", false, "form", ['modelForm'=>$form]);
    }

    public function actionEdit($id_group) {
        $menu = maze\table\MenuGroup::findOne($id_group);

        if (!$menu)
            throw new maze\exception\NotFoundHttpException(Text::_("Такого  меню не сущетсвует"));
        $form = $this->form('Menu');
        
        if ($this->request->isPost()) {
            
            $form->load($this->request->getPost());
            $form->code = $menu->code;
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-menu') {

                return json_encode(['errors' => FormBuilder::validate($form)]);
            }

            if ($this->request->isPost() && $this->request->getPost('Menu')) {

                if ($form->validate()) {
                    if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                        $menu = new maze\table\MenuGroup();
                        $form->name = $form->name . " - ( " . Text::_("EXP_MENU_ADD_ITEM_MESS_COPY") . " )";
                    }

                    $menu->setAttributes($form->getAttributes());

                    if ($menu->save()) {

                        $this->setMessage(Text::_("EXP_MENU_ADD_SAVE_MESS_YES"), 'success');
                        if ($this->request->get('action') == 'saveClose' || $this->request->get('action') == 'saveCopy') {
                            return $this->setRedirect([['run' => 'display']]);
                        }
                        return $this->setRedirect([['run' => 'edit', 'id_group' => $menu->id_group]]);
                    } else {
                        $this->setMessage($menu->getErrors(), 'error');
                    }
                } else {
                    $this->setMessage($form->getErrors(), 'error');
                }
                if ($menu->hasErrors() || $form->hasErrors()) {
                    RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'edit', 
                        'message'=>Text::_("EXP_MENU_ADD_SAVE_MESS_NO")]);
                }
            }
        }else{
            $form->setAttributes($menu->attributes, false);
        }

        return $this->renderPart("form", false, "form", ['menu' => $menu, 'modelForm'=>$form]);
    }

    public function actionClose() {
        $this->setMessage(Text::_("EXP_MENU_ADD_CLOSE_MESS_YES"), 'info');
        $this->setRedirect(['/admin/menu']);
    }
 public function actionTreemenu() {
//        if (!$this->_access->roles("menu", "EDIT_ITEM")) {
//            $this->sortgroup();
//        }
//
//        $post = $this->post;
//        $model_obj = $this->model_menu;
//
//        $menu = isset($post['param_item']) ? $post['param_item'] : false;
//        $order = isset($post['order_item']) ? $post['order_item'] : false;
//        $order_group = isset($post['order_group']) ? $post['order_group'] : false;
//        if ($order) {
//            foreach ($order as $ikey => $item) {
//                foreach ($menu as $key => $value) {
//                    if ($item['id'] == $value['id']) {
//                        $menu[$key]["order"] = $item['order'];
//                        unset($order[$ikey]);
//                    }
//                }
//            }
//            $menu = array_merge($menu, $order);
//        }
//
//        if ($menu) {
//            foreach ($menu as $item) {
//                $arr = array();
//                if (isset($item["parent"])) {
//                    $arr["parent"] = $item["parent"];
//                }
//                if (isset($item["group"])) {
//                    $arr["id_group"] = $item["group"];
//                }
//                if (isset($item["order"])) {
//                    $arr["ordering"] = $item["order"];
//                }
//
//
//                $model_obj->sortParentMenuItem($arr, $item["id"]);
//            }
//        }
//        if ($order_group) {
//
//            foreach ($order_group as $group) {
//                $arr = array();
//                if (isset($group["order"])) {
//                    $arr["ordering"] = $group["order"];
//                }
//
//                $model_obj->sortMenuGroup($arr, $group["group"]);
//            }
//        }
//        $this->sortgroup();
    }
    public function actionGettree() {
//        $model = $this->loadModel("menu");
//        ob_start();
//        echo $model->getTreeMenu("return cms.request(false, '/admin/menu/groupmenu/?idgroup=%2\$s&idparent=%1\$s')");
//        $buffer = ob_get_clean();
//
//        echo json_encode(array("html" => $buffer));
    }
}

?>