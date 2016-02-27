<?php

defined('_CHECK_') or die("Access denied");

use maze\table\Template;
use maze\table\InstallApp;
use maze\exception\NotFoundHttpException;

class SiteApp extends Application {

    public function getTheme() {

        if ($this->_theme === null) {
            $theme = null;

            if ($this->getRequest()->get('tmp_name') && $this->getAccess()->roles("system", "VIEW_TEMPLATE")) {

                $name = $this->getRequest()->get('tmp_name');
                $result = InstallApp::find()->where('type=:type AND name=:name', [':type' => 'template', ':name' => $name])->one();
                $theme = ['name' => $result->name, 'param' => null];
            } else {
                $result = RC::getDb()->cache(function($db){ 
                    return Template::find()->where(["front" => 1])->all();
                 }, null, 'fw_system');

                $cur_date = date("Y-m-d H:i:s"); // текущее время
                try {
                    if (isset($this->getRouter()->menu->id_tmp) && $this->getRouter()->menu->id_tmp) {
                        $id_tmp = $this->getRouter()->menu->id_tmp;
                    }

                    if (!isset($id_tmp)) {
                        $id_tmp = isset($this->getRouter()->exp->id_tmp) ? $this->getRouter()->exp->id_tmp : null;
                    }
                } catch (\Exception $ex) {
                    $id_tmp = null;
                }

                foreach ($result as $key => $tmp) {
                    if ($id_tmp) {
                        if ($tmp->id_tmp == $id_tmp) {
                            $this->_styleId = $tmp->id_tmp;
                            $theme = ['name' => $tmp->name, 'param' => $tmp->param];
                            break;
                        }
                    } else {
                        if ($tmp->time_active <= $cur_date && $tmp->time_inactive >= $cur_date) {
                            $this->_styleId = $tmp->id_tmp;
                            $theme = ['name' => $tmp->name, 'param' => $tmp->param];
                            break;
                        }
                    }
                }

                // преварительный просмотр стилей

                $viewtmp = $this->getRequest()->get('viewtmp');

                if ($viewtmp && $this->getConfig()->viewstyle && $this->getAccess()->roles("system", "VIEW_STYLE")) {
                    foreach ($result as $key => $tmp) {
                        if ($tmp->id_tmp == $viewtmp) {
                            $this->_styleId = $tmp->id_tmp;
                            $theme = ['name' => $tmp->name, 'param' => $tmp->param];
                            break;
                        }
                    }
                }

                if ($theme == null) {
                    foreach ($result as $key => $tmp) {
                        if ((int) $tmp->home == 1) {
                            $this->_styleId = $tmp->id_tmp;
                            $theme = ['name' => $tmp->name, 'param' => $tmp->param];
                            break;
                        }
                    }
                }
            }

            if ($theme) {
                $this->_theme = RC::createObject([
                            'class' => 'maze\base\Theme',
                            'name' => $theme['name'],
                            'basePath' => '@tmp/' . $theme['name'],
                            'param' => $theme['param'],
                            'front' => 1,
                            'baseUrl' => '@web/templates/' . $theme['name']
                ]);
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_DOCUMENT_GETTEMPLATE"), 500);
            }
        }

        return $this->_theme;
    }

    public function getToolbar() {
        if ($this->_toolbar == null) {
            $this->_toolbar = RC::createObject(['class' => 'ToolBarsite']);
        }
        return $this->_toolbar;
    }

    protected function loadToolBar() {

        if (!$this->access->roles("system", "VIEW_TOOLBAR") || $this->getRequest()->get("clear") == "ajax")
            return false;
        $lang = $this->getLang();
        $lang->front = 0;

        $application = $this->getMenu()->loadApp();
        $this->getMenu()->createMenu();

        foreach ($application as $app) {

            if (!$this->access->roles($app->name, "VIEW_ADMIN"))
                continue;

            $info = RC::getConf(["type" => "expansion", "name" => $app->name, "front" => 0]);

            $path = RC::getAlias('@admin/expansion/exp_' . $app->name . '/toolbarsite.php');

            try {
                if (file_exists($path)) {
                    @include_once $path;
                }
            } catch (Exception $ex) {
                RC::getLog()->add('error', [
                    'file' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage(),
                    'category' => get_class($ex)]);
                continue;
            }


            unset($info);
        }

        $toolbar = $this->getView()->render('@tmp/system/toolbar/toolbar');
        $lang->front = 1;
        return $toolbar;
    }

    public function dispatcher() {
        
        $this->loadSession();
        
        RC::getPlugin("system")->triggerHandler("initDispatcher");
        
        $this->loadUser();

        $widgets = RC::createObject(['class' => 'Widgets']);
        $meta = [];
        // перехватываем ошибки маршрутизатора
        try {
            $this->getRouter()->dispatcher();
            
            $id_menu = isset($this->getRouter()->menu->id_menu) ? $this->getRouter()->menu->id_menu : null;

            $this->getView()->widgets = $widgets->getWidgets($id_menu, $this->_styleId, $this->getRouter()->exp->id_exp);

            if ($this->getRouter()->menu) {

                if (!empty($this->getRouter()->menu->meta_title)) {
                    $meta["title"] = $this->getRouter()->menu->meta_title;
                } elseif ($this->getRouter()->menu->name) {
                    $meta["title"] = $this->getRouter()->menu->name;
                }

                if (!empty($this->getRouter()->menu->meta_des)) {
                    $meta["description"] = $this->getRouter()->menu->meta_des;
                }

                if (!empty($this->getRouter()->menu->meta_key)) {
                    $meta["keywords"] = $this->getRouter()->menu->meta_key;
                }

                if (!empty($this->getRouter()->menu->meta_robots)) {
                    $meta["robots"] = $this->getRouter()->menu->meta_robots;
                }

                $this->document->setBobyClass($this->getRouter()->menu->get('menu_body_class'));
            }

            
            $this->document->setBobyClass(['component-' . $this->getRouter()->exp->name, 'controller-' . $this->getRouter()->controller]);
            if ($this->getRouter()->view) {
                $this->document->setBobyClass('view-' . $this->getRouter()->view);
            }
            if ($this->getRouter()->layout) {
                $this->document->setBobyClass('view-layout-' . $this->getRouter()->layout);
            }
            $this->document->setHtmlClass('theme-' . $this->getTheme()->name);
            
            $isHome = $this->getRouter()->menu ? $this->getRouter()->menu->home : false;
            if($isHome || $this->getRouter()->getIsHome()){
                $this->document->setHtmlClass('front-page');
            }
        } catch (\Exception $exr) {

            $this->getView()->widgets = $widgets->getWidgets(null, $this->_styleId, null);
        }

        $this->document->setHtmlClass(['page-site', $this->request->gerBrowser(), $this->request->getOS()]);

        $meta = array_merge([
            "title" => $this->config->site_name,
            "description" => $this->config->meta_desc,
            "keywords" => $this->config->meta_keys,
            "robots" => $this->config->meta_robots,
            "author" => $this->config->meta_author
                ], $meta);


        $this->document->set("type", "text/html");
        $this->document->set("charset", $this->config->charset);
        $this->document->set("language", $this->config->language);
        $this->document->set("robots", $meta["robots"]);
        $this->document->set("author", $meta["author"]);
        $this->document->set("title", $meta["title"]);
        $this->document->set("description", $meta["description"]);
        $this->document->set("keywords", $meta["keywords"]);
        
        $this->document->set("favicon", $this->getTheme()->getUrl('favicon.png'));

        if ($this->config->get("enable_site") && !$this->access->roles("system", "VIEW_SITEDISABLE")) {
            $this->getView()->layout = "//disable";
            $this->document->set("title", Text::_("LIB_FRAMEWORK_APPLICATION_DISABLE_SITE"));
        }

        $getMode = $this->getRequest()->get("editing_mode");
        if ($getMode !== false) {
            $mode = $this->getSession()->set("editing_mode", $getMode);
        }
        if ($this->getSession()->isSess("editing_mode")) {
            $mode = $this->getSession()->get("editing_mode");
        } else {
            $mode = 0;
        }


        define('EDITING_MODE', $mode);

        RC::getPlugin("system")->triggerHandler("beforeDispatcher");

        $this->getView()->toolbar = $this->loadToolBar();
        if($this->getView()->toolbar){
            $this->document->setHtmlClass(['admin-toolbar']);
        }

        if (!isset($exr)) {
            $this->getView()->component = $this->loadExp();
        }
        
        RC::getPlugin("system")->triggerHandler("afterDispatcher");
        
        if (isset($exr)) {
            throw new NotFoundHttpException($exr->getMessage());
        }

        
        return $this;
    }

}

?>