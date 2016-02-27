<?php

defined('_CHECK_') or die("Access denied");

use ui\grid\GridFormat;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\table\Menu;
use ui\filter\FilterBuilder;
use maze\helpers\Html;
use maze\table\Routes;
use maze\helpers\StringHelper;

class Menu_Controller_Groupmenu extends Controller {

    public function accessFilter() {
        return [
            'publish unpublish add edit home copy sort moving pack' => ["menu", "EDIT_ITEM"],
            'delete' => ["menu", "DELET_ITEM"]
        ];
    }

    public function actionDisplay() {

        $modelFilter = $this->form('FilterGroup');
        $modelMenu = $this->model('Menu');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = maze\table\Menu::find()->joinWith(['group', 'lang', 'role', 'route'])->from(['m' => maze\table\Menu::tableName()]);
            
            $modelFilter->queryBilder($model);
            return (new GridFormat([
                'id' => 'groupmenu-all-grid',
                'model' => $model,
                'colonum' => 'm.ordering',
                'colonumData' => [
                    'id' => '$data->id_menu',
                    'ordering' => '"<span class=\"menu-icon-handle\"></span>"',
                    'parent',
                    'alias' => function($data) use ($modelMenu) {
                        return $data->route->alias .
                                '</span><div class="items-menu-path">' . $modelMenu->getMetaItems($data->paramLink, $data->typeLink) . '</div>';
                    },
                    'enabled',
                    'link'=>'$data->route->alias',
                    'home',
                    'id_menu',
                    'id_group' => function($data) {
                        return $data->group->name;
                    },
                    'name',
                    'lang' => function($data) {
                        return $data->id_lang ? $data->lang->title : Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_LANGALL");
                    },
                    'title_role' => function($data) {
                        if ($data->role) {
                            return implode(', ', array_map(function($role) {
                                                return $role->name;
                                            }, $data->role));
                        }
                        return Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_LANGALL");
                    },
                    'size' => function($data) {
                        return $data->countChild;
                    },
                    'paramLink' => function($data) use ($modelMenu) {
                        return $modelMenu->getMetaItems($data->paramLink, $data->typeLink);
                    }
                ]
            ]))->renderJson();
        }
        
        return $this->renderPart("allitems", false, false, ['modelFilter' => $modelFilter, "tableId"=>"groupmenu-all-grid"]);
    }
    
    public function actionMenu($id_group) {
        
        $modelFilter = $this->form('FilterGroup');
        $modelMenu = $this->model('Menu');

        if ($response = FilterBuilder::action($modelFilter)) {
            return json_encode($response);
        }

        if ($this->request->isAjax() && $this->request->get('clear') == 'ajax') {

            $model = maze\table\Menu::find()->joinWith(['group', 'lang', 'role', 'route'])->from(['m' => maze\table\Menu::tableName()]);
            $model->where(['m.id_group' => $id_group]);
            
            $modelFilter->queryBilder($model);
            return (new GridFormat([
                'id' => 'groupmenu-grid',
                'model' => $model,
                'mode' => 'tree',
                'link' => 'parent',
                'colonum' => 'ordering',
                'colonumData' => [
                    'id' => '$data->id_menu',
                    'ordering' => '"<span class=\"menu-icon-handle\"></span>"',
                    'parent',
                    'alias' => function($data) use ($modelMenu) {
                        return $data->route->alias .
                                '</span><div class="items-menu-path">' . $modelMenu->getMetaItems($data->paramLink, $data->typeLink) . '</div>';
                    },
                    'enabled',
                    'link'=>'$data->route->alias',
                    'home',
                    'id_menu',
                    'typetree' => '"article"',
                    'id_group' => function($data) {
                        return $data->group->name;
                    },
                    'name',
                    'lang' => function($data) {
                        return $data->id_lang ? $data->lang->title : Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_LANGALL");
                    },
                    'title_role' => function($data) {
                        if ($data->role) {
                            return implode(', ', array_map(function($role) {
                                                return $role->name;
                                            }, $data->role));
                        }
                        return Text::_("EXP_MENU_VIEW_GROUP_TABL_ITEM_LANGALL");
                    },
                    'size' => function($data) {
                        return $data->countChild;
                    },
                    'paramLink' => function($data) use ($modelMenu) {
                        return $modelMenu->getMetaItems($data->paramLink, $data->typeLink);
                    }
                ]
                    ]))->renderJson();
        }
        
        return parent::display([
            'id_group' => $id_group, 
            'modelFilter' => $modelFilter,
            "tableId"=>"groupmenu-grid"
        ]);
    }

    public function actionAdd() {
        $modelForm = $this->form('Item', ['scenario' => $this->request->get('typeLink', ['mysql'], 'expansion')]);
        $modelForm->typeLink = $modelForm->scenario;
        $modelForm->setAttributes($this->request->get());
        $modelMenu = $this->model('Menu');
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post(null, 'none'));
        
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-groupmenu') {
                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }
            if ($modelForm->validate()) {
                if ($modelMenu->saveItem($modelForm)) {
                    $this->setMessage(Text::_("EXP_MENU_GROUP_SAVE_MESS_YES"), 'success');
                    if ($this->request->get('action') == 'saveClose') {
                        return $this->setRedirect([['run'=>'menu','id_group' => $modelForm->id_group]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_menu' => $modelForm->id_menu]]);
                } else {
                    $this->setMessage(Text::_("EXP_MENU_GROUP_SAVE_MESS_NO"), "error");
                }
            }
        }
        return $this->renderPart("form", false, "form", ['modelForm' => $modelForm, 'modelMenu' => $modelMenu]);
    }

    public function actionEdit($id_menu) {
        $modelForm = $this->form('Item', ['scenario' => $this->request->get('typeLink', ['mysql'], 'expansion')]);
        $modelForm->typeLink = $modelForm->scenario;
        $modelForm->setAttributes($this->request->get());
        $modelMenu = $this->model('Menu');

        $item = maze\table\Menu::find()->joinWith('route')->where(['id_menu'=>$id_menu])->one();

        if (!$item) {
            throw new maze\exception\NotFoundHttpException("Такого  пункта меню не сущетсвует");
        }
        $route = $item->route;
       
        $modelForm->routes_id = $route->routes_id;
        if ($this->request->isPost()) {
            $post = $this->request->post('Item');
            if($post && isset($post['typeLink']) && !empty($post['typeLink'])){
                $modelForm->scenario = $post['typeLink'];
            }
            $modelForm->load($this->request->post(null, 'none'));
            
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-groupmenu') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }
            if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                $modelForm->name = $modelForm->name . " - ( " . Text::_("EXP_MENU_ADD_ITEM_MESS_COPY") . " )";
                $modelForm->id_menu = null;
                $modelForm->alias = $modelForm->alias . '-copy';
            }
            if ($modelForm->validate()) {
                if ($modelMenu->saveItem($modelForm)) {
                    if ($this->request->get('action') == 'saveCopy' || $this->request->get('action') == 'copy') {
                        $this->setMessage(Text::_("EXP_MENU_GROUP_COPYCLOSE_MESS_YES"), 'success');
                    } else {
                        $this->setMessage(Text::_("EXP_MENU_GROUP_SAVE_MESS_YES"), 'success');
                    }
                    if ($this->request->get('action') == 'saveClose' || $this->request->get('action') == 'saveCopy') {
                        return $this->setRedirect([['run'=>'menu', 'id_group' => $modelForm->id_group]]);
                    }
                    return $this->setRedirect([['run' => 'edit', 'id_menu' => $modelForm->id_menu]]);
                } else {
                    $this->setMessage(Text::_("EXP_MENU_GROUP_SAVE_MESS_NO"), "error");
                }
            }
        } else {
            if(!$this->request->get('typeLink')){
                $modelForm->scenario = $item->typeLink;
            }
            $modelForm->setAttributes($item->attributes);
            $modelForm->alias = $route->alias;
            $modelForm->meta_title = $route->meta_title;        
            $modelForm->meta_key = $route->meta_keywords;            
            $modelForm->meta_des = $route->meta_description;
            $modelForm->meta_robots = $route->meta_robots;
            
            if ($item->typeLink == 'expansion' && !$modelForm->layout) {
                $modelForm->component = $item->paramLink['component'];
                $modelForm->controller = $item->paramLink['controller'];
                $modelForm->view = $item->paramLink['view'];
                $modelForm->layout = $item->paramLink['layout'];
                $modelForm->url_param = $item->paramLink['url_param'];
            }
            
            if($this->request->get('typeLink')){
                $modelForm->scenario = $this->request->get('typeLink');
                $modelForm->setAttributes($this->request->get());
            }
            
            if($modelForm->typeLink == 'url' || $modelForm->typeLink == 'alias'){
                if(!is_string($modelForm->paramLink)){
                    $modelForm->paramLink = '';
                }
            }
             
            if ($role = $item->role) {
                $modelForm->id_role = array_map(function($role) {
                    return $role->id_role;
                }, $role);
            }
        }

        return $this->renderPart("form", false, "form", ['modelForm' => $modelForm, 'modelMenu' => $modelMenu]);
    }

    public function actionPublish(array $id_menu) {

        if (empty($id_menu))
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_MENU_ITEMS_EMPTY"));
            Menu::updateAll(['enabled' => 1], ['id_menu' => $id_menu]);
        if ($this->request->isAjax()) {
            return;
        }
        $this->setMessage(Text::_("EXP_MENU_GROUP_MESS_PUBLISH"), 'success');
        $id_group = Menu::find()->where(['id_menu' => $id_menu])->one();
        $id_group = $id_group ? $id_group->id_group : null;
        $this->setRedirect([['id_group' => $id_group]]);
    }

    public function actionUnpublish(array $id_menu) {
        if (empty($id_menu))
            throw new maze\exception\NotFoundHttpException(Text::_("EXP_MENU_ITEMS_EMPTY"));
        Menu::updateAll(['enabled' => 0], ['id_menu' => $id_menu]);
        if ($this->request->isAjax()) {
            return;
        }
        $this->setMessage(Text::_("EXP_MENU_MESS_UNPUBLISH"), 'success');
        $id_group = Menu::find()->where(['id_menu' => $id_menu])->one();
        $id_group = $id_group ? $id_group->id_group : null;
        $this->setRedirect([['id_group' => $id_group]]);
    }

    public function actionParent($id_group) {
        $modelMenu = $this->model('Menu');
        return json_encode(['html' => $modelMenu->listItems($id_group, $this->request->get('id_menu'))]);
    }

    public function actionClose($id_group = null) {
        $this->setMessage(Text::_("EXP_MENU_ADD_CLOSE_MESS_YES"), 'info');
        if($id_group){
            $path = [['run'=>'menu', 'id_group' =>$id_group]];
        }else{
            $path = ['/admin/menu/groupmenu'];
        }
        $this->setRedirect($path);
    }

    public function actionDelete(array $id_menu) {
        $menu = $this->model('Menu');
        $item = Menu::find()->where(['id_menu' => $id_menu])->one();
        if ($menu->deleteItems($id_menu)) {
            $this->setMessage(Text::_("EXP_MENU_GROUP_DEL_MESS_YES"), 'success');
        } else {
             RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'delete', 
                        'message'=>Text::_("LIB_FRAMEWORK_EXPANSION_DELETE_ERR", array("Menu_Controller_Groupmenu->delete()"))
                 ]);
            $this->setMessage(Text::_("EXP_MENU_DELETE_ERROR"), 'error');
        }
        $this->setRedirect([['run'=>'menu', 'id_group' => $item->id_group]]);
    }

    public function actionCopy(array $id_menu) {

        if ($this->model('Menu')->copy($id_menu)) {
            $this->setMessage(Text::_("EXP_MENU_GROUP_COPYREC_MESS_YES"), "success");
        } else {
            $this->setMessage(Text::_("EXP_MENU_GROUP_COPYREC_MESS_NO"), "error");
             RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'copy', 
                        'message'=>Text::_("EXP_MENU_GROUP_COPYREC_MESS_NO")
                 ]);
            return $this->setRedirect([['run' => 'display']]);
        }
        $item = Menu::find()->where(['id_menu' => $id_menu])->one();
        $this->setRedirect([['id_group' => $item->id_group]]);
    }

    public function actionSort($id_menu, array $sort, $parent) {
        if (!$this->request->isAjax()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("EXP_MENU_ITEMS_EMPTY"));
        }
        if ($id_menu) {
            $menu = Menu::find()->where(['id_menu' => $id_menu])->one();
            if ($menu) {
                $menu->parent = $parent;
                $menu->save();
            }
        }

        if ($sort) {
            foreach ($sort as $item) {
                if (isset($item['id_menu']) && isset($item['ordering'])) {
                    $itemA = Menu::find()->where(['id_menu' => $item['id_menu']])->one();
                    $itemA->ordering = $item['ordering'];
                    $itemA->save();
                }
            }
        }
    }
    
    public function actionHome($id_menu) {
        if (!$this->request->isAjax()) {
            throw new maze\exception\NotAcceptableHttpException(Text::_("EXP_MENU_ITEMS_EMPTY"));
        }
        
        if(!$this->model('Menu')->home($id_menu))
        {
            throw new maze\exception\NotFoundHttpException(Text::_("Ошибка назначения пукта главным"));
        }
        
    }
    public function actionMoving(array $id_menu) {
        $modelMenu = $this->model('Menu');
        $modelForm = $this->form('Moving');
        $modelForm->id_menu = $id_menu;
        $menu = Menu::find()->where(['id_menu' => $id_menu])->one();
        $modelForm->id_group = $menu->id_group;
        
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-moving') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }
            $modelForm->id_menu = $id_menu;
            if($modelForm->validate() && $modelMenu->move($modelForm))
            {
                $this->setMessage(Text::_("EXP_MENU_GROUP_MOVING_YES"), "success");
            }
            else
            {
                $this->setMessage(Text::_("EXP_MENU_GROUP_MOVING_NO"), "error");
                RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'moving', 
                        'message'=>Text::_("EXP_MENU_GROUP_MOVING_NO")
                 ]);
            }
            $this->setRedirect([['run'=>'menu', 'id_group'=>$modelForm->id_group]]);
            
        }
        return $this->renderPart("moving", false, "pack", [
            'modelForm' => $modelForm, 
            'modelMenu' => $modelMenu
        ]); 
    }

    public function actionPack(array $id_menu) {
        $modelMenu = $this->model('Menu');
        $modelForm = $this->form('Pack');
        $modelForm->id_menu = $id_menu;
                        
        if ($this->request->isPost()) {
            $modelForm->load($this->request->post());
            
            if ($this->request->isAjax() && $this->request->get('checkform') == 'menu-form-pack') {

                return json_encode(['errors' => FormBuilder::validate($modelForm)]);
            }

            if($modelForm->validate() && $modelMenu->pack($modelForm))
            {
                $this->setMessage(Text::_("EXP_MENU_GROUP_PACK_YES"), "success");
            }
            else
            {
                $this->setMessage(Text::_("EXP_MENU_GROUP_PACK_ERR"), "error");
                RC::getLog()->add('exp',['component'=>'menu',
                        'category'=>__METHOD__,
                        'action'=>'pack', 
                        'message'=>Text::_("EXP_MENU_GROUP_PACK_ERR")
                 ]);
            }
            $menu = Menu::find()->where(['id_menu' => $id_menu])->one();
            $this->setRedirect([['run'=>'menu', 'id_group'=>$menu->id_group]]);
            
        }
        return $this->renderPart("pack", false, "pack", [
            'modelForm' => $modelForm, 
            'modelMenu' => $modelMenu
        ]); 
    }

}

?>