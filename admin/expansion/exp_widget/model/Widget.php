<?php

namespace exp\exp_widget\model;

use maze\helpers\ArrayHelper;
use maze\table\Template;
use maze\table\Menu;
use maze\table\Expansion;
use maze\table\InstallApp;
use maze\table\Widgets;
use maze\table\WidgetsExp;
use maze\table\WidgetsMenu;
use maze\table\WidgetsUrl;
use maze\table\AccessRole;
use exp\exp_widget\form\FormWidget;
use maze\document\View;
use RC;

class Widget extends \maze\base\Model {

    public function getPositionTemplate($id_tmp, $front) {
        $condition = [];
        if ($id_tmp) {
            $condition = ['id_tmp' => $id_tmp, 'front' => $front];
        } else {
            $condition = ['front' => $front];
        }
        $tmp = Template::find()->distinct('name')->where($condition)->all();
        $result = [];
        foreach ($tmp as $t) {
            $position = View::getWidgetsPosiotion($t->name, $t->front);

            $result = array_merge($result, array_combine($position, $position));
        }
        return $result;
    }

    public function getPositionShortCode() {
        $shortcode = \RC::app()->getComponent('widget')->config->getVar('shortcode');
        if ($shortcode) {
            $shortcode = preg_split("/,[\s]*/s", $shortcode);
            $shortcode = array_combine($shortcode, $shortcode);
        } else {
            $shortcode = [];
        }
        return $shortcode;
    }

    public function getAllPosition($front) {
        $result = $this->getPositionTemplate(null, $front);
        if ($front) {
            $result = array_merge($this->getPositionShortCode(), $result);
        }

        return $result;
    }

    public function getPosition($id_tmp) {
        $result = [];
        if ($id_tmp !== null && $id_tmp == 0) {
            $result = $this->getPositionShortCode();
        } elseif ($id_tmp) {
            $tmp = Template::find()->distinct('name')->where(['id_tmp' => $id_tmp])->all();

            foreach ($tmp as $t) {
                $position = View::getWidgetsPosiotion($t->name, $t->front);

                $result = array_merge($result, array_combine($position, $position));
            }
        }

        return $result;
    }

    public function getWidgetPosition($id_tmp, $position) {
        return Widgets::find()->where(['id_tmp' => $id_tmp, 'position' => $position])->orderBy('ordering')->asArray()->all();
    }

    public function getWidgetList($front) {
        $widgets = InstallApp::find()->where(['type' => 'widget', 'front_back' => $front])->all();
        $option = [];

        foreach ($widgets as $wid) {
            $conf = \RC::getConf(array("name" => $wid->name, "type" => "widget", "front" => $front));
            $option[$wid->name] = $conf->get("name") . ' [' . $wid->name . ']';
        }
        return $option;
    }

    public function getTemplate($condition = []) {
        $result = ArrayHelper::map(Template::find()->where($condition)->asArray()->all(), 'id_tmp', 'title');
        $result['0'] = \Text::_('EXP_WIDGET_WIDGETS_FILTER_TMP_CODE');
        return $result;
    }

    public function hasWidget($name, $front) {
        return InstallApp::find()->where(['type' => 'widget', 'name' => $name, 'front_back' => $front])->exists();
    }

    public function getWidget($front) {
        $widgets = InstallApp::find()->where(['type' => 'widget', 'front_back' => $front])->all();
        $refult = [];
        foreach ($widgets as $wid) {
            $conf = \RC::getConf(array("name" => $wid->name, "type" => "widget", "front" => $front));
            $refult[] = [
                'name' => $wid->name,
                'front' => $wid->front_back,
                'title' => $conf->get("name"),
                'description' => $conf->get("description"),
                'version' => $conf->get("version")
            ];
        }

        return $refult;
    }

    public function saveWidget($form) {
        $transaction = \RC::getDb()->beginTransaction();
        try {
            if (!$form->validate()) {
                throw new \Exception();
            }

            if ($form->id_wid) {
                $wid = Widgets::findOne($form->id_wid);
            } else {
                $wid = new Widgets();
            }

            $wid->attributes = $form->attributes;

            if (!$wid->save()) {
                $form->addError('id_wid', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                throw new \Exception();
            }

            AccessRole::deleteAll(['exp_name' => 'widget', 'key_role' => 'widget', 'key_id' => $wid->id_wid]);
            if (!empty($form->id_role) && is_array($form->id_role)) {
                foreach ($form->id_role as $id_role) {
                    $role = new AccessRole();
                    $role->exp_name = 'widget';
                    $role->key_role = 'widget';
                    $role->key_id = $wid->id_wid;
                    $role->id_role = $id_role;
                    if (!$role->save()) {
                        $form->addError('id_role', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                        throw new \Exception();
                    }
                }
            }
            WidgetsExp::deleteAll(['id_wid' => $wid->id_wid]);
            if (!empty($form->id_exp) && is_array($form->id_exp)) {
                foreach ($form->id_exp as $id_exp) {
                    $exp = new WidgetsExp();
                    $exp->id_wid = $wid->id_wid;
                    $exp->id_exp = $id_exp;
                    if (!$exp->save()) {
                        $form->addError('id_exp', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                        throw new \Exception();
                    }
                }
            }
            WidgetsMenu::deleteAll(['id_wid' => $wid->id_wid]);
            
            if (!empty($form->id_menu) && is_array($form->id_menu)) {
                foreach ($form->id_menu as $id_menu) {
                    if(empty($id_menu)) continue;
                    $menu = new WidgetsMenu();
                    $menu->id_wid = $wid->id_wid;
                    $menu->id_menu = $id_menu;
                    if (!$menu->save()) {
                        $form->addError('id_menu', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                        throw new \Exception();
                    }
                }
            }
            WidgetsUrl::deleteAll(['id_wid' => $wid->id_wid]);
            if (!empty($form->url) && is_array($form->url)) {
                foreach ($form->url as $i => $url) {
                    $objUrl = new WidgetsUrl(['scenario' => $url['method']]);
                    $objUrl->attributes = $url;
                    $objUrl->id_wid = $wid->id_wid;
                    $objUrl->sort = $i + 1;
                    if ($objUrl->validate()) {
                        if (!$objUrl->save()) {
                            $form->addError('url', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                            throw new \Exception();
                        }
                    }
                }
            }
            if (!empty($form->sort) && is_array($form->sort)) {
                foreach ($form->sort as $key => $id_wid) {
                    if ($id_wid == 'self') {
                        $widObj = Widgets::findOne($wid->id_wid);
                    } else {
                        $widObj = Widgets::findOne($id_wid);
                    }
                    if ($widObj) {
                        $widObj->ordering = $key + 1;
                        if (!$widObj->save()) {
                            $form->addError('sort', \Text::_('EXP_WIDGET_CONTROLLER_MESS_SAVE_ERROR'));
                            throw new \Exception();
                        }
                    }
                }
            }
            $form->id_wid = $wid->id_wid;
            RC::getCache("fw_widgets")->clearTypeFull();
            $transaction->commit();
        } catch (\Exception $e) {
            
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    public function delete($id_wid) {
        $widgets = Widgets::findAll(['id_wid' => $id_wid]);
        if (!empty($widgets)) {
            foreach ($widgets as $wid) {
                $wid->delete();
            }
            RC::getCache("fw_widgets")->clearTypeFull();
            return true;
        }
        return false;
    }

    public function enable($id_wid, $enable) {
        Widgets::updateAll(['enabled' => $enable], ['id_wid' => $id_wid]);
        RC::getCache("fw_widgets")->clearTypeFull();
    }

    public function copy($objWibget) {
        $form = new FormWidget();
        $form->attributes = $objWibget->attributes;
        $form->id_exp = array_map(function($arr) {
            return $arr->id_exp;
        }, $objWibget->exp);
        $form->id_menu = array_map(function($arr) {
            return $arr->id_menu;
        }, $objWibget->menu);
        $form->url = array_map(function($arr) {
            return $arr->attributes;
        }, $objWibget->url);
        $form->id_role = array_map(function($arr) {
            return $arr->id_role;
        }, $objWibget->accessRole);
        $form->id_wid = null;
        $form->title .= " - ( " . \Text::_("EXP_WIDGET_TITLE_COPY") . " )";
        if ($form->validate()) {
            return $this->saveWidget($form);
        }
        RC::getCache("fw_widgets")->clearTypeFull();
        return false;
    }

    public function pack($id_wid, $form) {
        $widgets = Widgets::findAll(['id_wid' => $id_wid]);
        if ($widgets) {
            foreach ($widgets as $wid) {
                $wid->attributes = $form->attributes;
                $wid->save();
                AccessRole::deleteAll(['exp_name' => 'widget', 'key_role' => 'widget', 'key_id' => $wid->id_wid]);
                if (!empty($form->id_role) && is_array($form->id_role)) {
                    foreach ($form->id_role as $id_role) {
                        $role = new AccessRole();
                        $role->exp_name = 'widget';
                        $role->key_role = 'widget';
                        $role->key_id = $wid->id_wid;
                        $role->id_role = $id_role;
                        $role->save();
                    }
                }
            }
        }
        RC::getCache("fw_widgets")->clearTypeFull();
    }

}
