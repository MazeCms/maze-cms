<?php

namespace exp\exp_menu\model;

use maze\helpers\ArrayHelper;
use maze\table\InstallApp;
use maze\table\MenuGroup;
use maze\table\Roles;
use maze\table\Languages;
use maze\table\Template;
use maze\table\AccessRole;
use maze\table\Routes;

class Menu extends \maze\base\Model {

    public function listTypeLink() {
        return [
            "expansion" => \Text::_("EXP_MENU_FILTER_ITEM_TYPELINK_OPTION_EXP"),
            "url" => \Text::_("EXP_MENU_FILTER_ITEM_TYPELINK_OPTION_URL"),
            "alias" => \Text::_("EXP_MENU_ADD_ITEM_TMP_REDIRECT_INTERNAL_NAME"),
            "separator" => \Text::_("EXP_MENU_ADD_ITEM_TMP_REDIRECT_SEPARATOR_NAME")
        ];
    }

    public function listRole() {
        return Roles::getList();
    }

    public function listLang() {
        return Languages::getList();
    }

    public function getChildId($parents, $id_menu) {
        if (!isset($parents[$id_menu]))
            return [];
        $disID = [];
        foreach ($parents[$id_menu] as $item) {
            $disID[] = $item->id_menu;
            $disID = array_merge($disID, $this->getChildId($parents, $item->id_menu));
        }

        return $disID;
    }

    public function getParnetsItem($id_group) {
        $items = \maze\table\Menu::find()->joinWith('route')->where(['id_group' => $id_group])->orderBy('ordering')->all();
        $parents = [];
        foreach ($items as $item) {
            $parents[$item->parent][] = $item;
        }
        return $parents;
    }

    public function listItems($id_group, $id_menu = null) {
        if (empty($id_group))
            return [];

        $items = \maze\table\Menu::find()->joinWith('route')->where(['id_group' => $id_group])->orderBy('ordering')->all();
        if (!$items)
            return [];
        $disID = [];
        if ($id_menu) {
            $parents = [];
            foreach ($items as $item) {
                $parents[$item->parent][] = $item;
            }
            if (!is_array($id_menu)) {
                $id_menu = [$id_menu];
            }
            foreach ($id_menu as $id) {
                $disID = array_merge($disID, $this->getChildId($parents, $id));
            }


            $disID = array_merge($id_menu, $disID);
        }
        $result = [];
        foreach ($items as $item) {
            $disabled = false;
            $disabled = !empty($disID) && in_array($item->id_menu, $disID);
            $result[] = ['label' => $item->name, 'value' => $item->id_menu, 'parent' => $item->parent, 'disabled' => $disabled];
        }
        return $result;
    }

    public function listMenu() {
        return \maze\table\MenuGroup::getList();
    }

    public function listTmp() {
        return Template::getList();
    }

    public function listExp() {
        return InstallApp::getListExp();
    }

    public function saveItem($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if ($form->id_menu) {
                $menu = \maze\table\Menu::find()->joinWith('route')->where(['id_menu'=>$form->id_menu])->one();
                $oldParent = $this->getParnetsItem($menu->id_group);
                $idChild = $this->getChildId($oldParent, $menu->id_menu);
                if ($idChild) {
                    \maze\table\Menu::updateAll(['id_group' => $form->id_group], ['in', 'id_menu', $idChild]);
                }
                $route = $menu->route;
            } else {
                $menu = new \maze\table\Menu();
                $menu->ordering = \maze\table\Menu::find()
                                ->where(['id_group' => $form->id_group])
                                ->andFilterWhere(['parent' => $form->parent])->count() + 1;
                
                $route = new Routes();                
            }
            
            $route->expansion = 'menu';
            $route->alias = $form->alias;
            $route->meta_title = $form->meta_title;
            $route->meta_keywords = $form->meta_key;
            $route->meta_description = $form->meta_des;
            $route->meta_robots = $form->meta_robots;
            

            $menu->attributes = $form->attributes;

            if ($form->typeLink == 'expansion') {
                $menu->paramLink = [
                    'component' => $form->component,
                    'controller' => $form->controller,
                    'view' => $form->view,
                    'layout' => $form->layout,
                    'url_param' => $form->url_param
                ];

                $component = \maze\table\Expansion::find()->where(['name' => $form->component])->one();
                if (!$component) {
                    $form->addError('id_exp', 'Данного приложения не существует');
                    throw new \Exception();
                }
               
                
                $menu->id_exp = $component->id_exp;

                if ($form->home) {
                    \maze\table\Menu::updateAll(['home' => 0], ['home' => 1, 'id_lang' => $form->id_lang]);
                }
            }
            
            
            
            $route->save(false);
            $menu->routes_id = $route->routes_id;

            if (!$menu->save()) {
                $form->addError('id_exp', 'Ошибка сохранения пункта  меню');
                throw new \Exception();
            }

            AccessRole::deleteAll(['exp_name' => 'menu', 'key_role' => 'items', 'key_id' => $menu->id_menu]);
            if (!empty($form->id_role) && is_array($form->id_role)) {
                foreach ($form->id_role as $id_role) {
                    $role = new AccessRole();
                    $role->exp_name = 'menu';
                    $role->key_role = 'items';
                    $role->key_id = $menu->id_menu;
                    $role->id_role = $id_role;
                    if (!$role->save()) {
                        $form->addError('id_role', 'Ошибка сохранения роли пункта  меню');
                        throw new \Exception();
                    }
                }
            }
            $form->id_menu = $menu->id_menu;
            \RC::getCache("fw_menu")->clearTypeFull();
            $transaction->commit();
            \RC::getPlugin("menu")->triggerHandler("afterSaveItem", [$form->id_menu]);
        } catch (\Exception $e) {
            
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function move($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {

            $menu = \maze\table\Menu::find()->where(['id_menu' => $form->id_menu])->one();
            $oldParent = $this->getParnetsItem($menu->id_group);
            $idChild = [];
            foreach ($form->id_menu as $id) {
                $idChild = array_merge($idChild, $this->getChildId($oldParent, $id));
            }
            $idChild = array_merge($idChild, $form->id_menu);
            if ($idChild) {
                \maze\table\Menu::updateAll(['id_group' => $form->id_group], ['in', 'id_menu', $idChild]);
            }

            \maze\table\Menu::updateAll(['parent' => $form->parent], ['in', 'id_menu', $form->id_menu]);
            \RC::getCache("fw_menu")->clearTypeFull();
            \RC::getPlugin("menu")->triggerHandler("afterMoveItem", [$form->id_menu]);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function home($id_menu) {
        $item = \maze\table\Menu::find()->where(['id_menu' => $id_menu])->one();
        if ($item) {
            \maze\table\Menu::updateAll(['home' => 0], ['id_lang' => $item->id_lang]);
            $item->home = 1;
            if ($item->save())
                \RC::getCache("fw_menu")->clearTypeFull();
                return true;
        }

        return false;
    }

    public function deleteItems($id_menu) {
        $menu = \maze\table\Menu::find()->where(['id_menu' => $id_menu])->one();
        if (!$menu)
            return false;

        $parents = $this->getParnetsItem($menu->id_group);
        $id = [];

        foreach ($id_menu as $id_m) {
            $id = array_merge($id, $this->getChildId($parents, $id_m));
            $id[] = $id_m;
        }
        $items = \maze\table\Menu::find()->joinWith('route')->where(['id_menu'=>$id])->all();
        foreach($items as $item){
            if($item->route) $item->route->delete();
            $item->delete();
        }
        \RC::getCache("fw_menu")->clearTypeFull();
        \RC::getPlugin("menu")->triggerHandler("afterDeleteItem", [$id_menu]);
        return $id;
    }
    

    public function pack($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {

            $items = \maze\table\Menu::find()->joinWith('route')->where(['id_menu' => $form->id_menu])->all();

            if (!$items) {
                $form->addError('id_menu', 'Ошибка отсутсвуют пункты меню');
                throw new \Exception();
            }

            foreach ($items as $item) {
                $item->route->meta_robots = $form->meta_robots;
                
                $item->time_active = $form->time_active;
                $item->time_inactive = $form->time_inactive;
                $item->id_tmp = $form->id_tmp;
                $item->id_lang = $form->id_lang;
                
                $item->route->save();
                
                if (!$item->save()) {
                    $form->addError('id_menu', 'Ошибка сохранения пункта (' . $item->id_menu . ') меню');
                    throw new \Exception();
                }
                AccessRole::deleteAll(['exp_name' => 'menu', 'key_role' => 'items', 'key_id' => $item->id_menu]);
                if (!empty($form->id_role) && is_array($form->id_role)) {
                    foreach ($form->id_role as $id_role) {
                        $role = new AccessRole();
                        $role->exp_name = 'menu';
                        $role->key_role = 'items';
                        $role->key_id = $item->id_menu;
                        $role->id_role = $id_role;
                        if (!$role->save()) {
                            $form->addError('id_role', 'Ошибка сохранения роли пункта  меню');
                            throw new \Exception();
                        }
                    }
                }
            }
            \RC::getCache("fw_menu")->clearTypeFull();
            \RC::getPlugin("menu")->triggerHandler("afterPackItem", [$form->id_menu]);
            $transaction->commit();
        } catch (\Exception $e) {
            $form->addError('id_menu', $e->getMessage());
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function copyGroup($id_group) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            $menu = MenuGroup::findAll(['id_group' => $id_group]);
            if (!$menu)
                throw new \Exception();
            foreach ($menu as $m) {
                $copy = new MenuGroup();
                $pattern = $m->getAttributes();
                $pattern['code'] = \RC::app()->session->generateCode(15);
                $pattern['name'] = $pattern['name'] . " - ( " . \Text::_("EXP_MENU_ADD_ITEM_MESS_COPY") . " )";

                $copy->setAttributes($pattern);
                if ($copy->save()) {
                   if($parents = $this->getParnetsItem($m->id_group))
                   {                      
                        $this->copyChildItem($parents, 0, 0, $copy->id_group);
                   }
                } else {
                    throw new \Exception();
                }
            }
            \RC::getCache("fw_menu")->clearTypeFull();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
    
    public function copyChildItem($parents, $parent, $new_parent, $new_group) {
        if (!isset($parents[$parent])) return;
  
        foreach ($parents[$parent] as $item) {
            $itemNew = new \maze\table\Menu();
            
            $itemNew->attributes = $item->attributes;
            $itemNew->home = 0;
            $itemNew->parent = $new_parent;
            $itemNew->id_group = $new_group;
            $route = new Routes();
            $route->attributes = $item->route->attributes;
            $route->alias =  \RC::app()->session->generateCode(30);
            if(!$route->save()){
                 throw new \Exception();
            }
            $itemNew->routes_id = $route->routes_id;
            if(!$itemNew->save())
            {
                throw new \Exception();
            }
            
            $this->copyChildItem($parents, $item->id_menu, $itemNew->id_menu, $new_group);
        }
    }
    
    public function copy($id_menu){
         $transaction = \RC::getDb()->beginTransaction();
         $items = \maze\table\Menu::find()->joinWith('route')->where(['id_menu' => $id_menu])->all();
        try {
              foreach ($items as $item) {
                $menu = new \maze\table\Menu();
                $menu->attributes = $item->attributes;
                $menu->name .= " - ( " . \Text::_("EXP_MENU_ADD_ITEM_MESS_COPY") . " )";
                $route = new \maze\table\Routes();
                
                $route->attributes = $item->route->attributes;
                $route->alias =  \RC::app()->session->generateCode(15);
                if(!$route->save()){
                    continue;
                }
                $menu->routes_id = $route->routes_id;
                $menu->home = 0;
                if (!$menu->save()) {
                    throw new \Exception();
                }
            }
            \RC::getCache("fw_menu")->clearTypeFull();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function getMetaItems($params, $type) {
        $result = [];

        switch ($type) {
            case 'expansion':

                if (!isset($params["component"]))
                    return "";

                $confExp = \RC::getConf(array("type" => "expansion", "name" => $params["component"]));

                $result[] = \Text::_($confExp->get("name"));

                if (isset($params["view"])) {
                    $path = PATH_ROOT . DS . 'expansion' . DS . "exp_" . $params["component"] . DS . 'views' . DS . $params["view"] . DS . "meta" . DS . "meta." . $params["view"] . ".xml";

                    $conf = new \XMLConfig($path);
                    if ($conf->get("title")) {
                        $result[] = \Text::_($conf->get("title"));
                        $xml = $conf->getXML();
                        foreach ($xml->layoutset as $lay) {
                            if (isset($params["layout"]) && isset($lay->layout) && $params["layout"] == $lay->layout) {
                                $result[] = \Text::_($lay->title);
                                break;
                            }
                        }
                    }
                }

                $result = implode("/", $result);

                break;

            case 'alias':

                $result = \Text::_("EXP_MENU_ADD_ITEM_TMP_REDIRECT_INTERNAL_NAME") . ": " . $params;

                break;

            case 'url':

                $result = \Text::_("EXP_MENU_ADD_ITEM_TMP_REDIRECT_URL_NAME") . ": " . $params;

                break;

            case 'separator':

                $result = \Text::_("EXP_MENU_ADD_ITEM_TMP_REDIRECT_SEPARATOR_NAME");

                break;
        }

        return $result;
    }

}
