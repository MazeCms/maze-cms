<?php
defined('_CHECK_') or die("Access denied");
use maze\table\Template;
use maze\table\InstallApp;

class AdminApp extends Application {

   

    public function getToolbar() {
        if ($this->_toolbar == null) {
            $this->_toolbar = RC::createObject(['class' => 'ToolBarAdmin']);
        }
        return $this->_toolbar;
    }

    
    public function getTheme() {

        if ($this->_theme === null) {
            $theme = null;

            if ($this->getRequest()->get('tmp_name') && $this->getAccess()->roles("system", "VIEW_TEMPLATE")) {

                $name = $this->getRequest()->get('tmp_name');
                $result = InstallApp::find()->where('type=:type AND name=:name', [':type' => 'template', ':name' => $name])->one();
                $theme = ['name' => $result->name, 'param' => null];
            } else {
                $result = RC::getDb()->cache(function($db){  
                    return Template::find()->where(["front" => 0])->all();
                }, null, 'fw_system');
  
                $cur_date = date("Y-m-d H:i:s"); // текущее время
                try {
                  
                    $id_tmp = isset($this->getRouter()->exp->id_tmp) ? $this->getRouter()->exp->id_tmp : null ;
                  
                } catch (\Exception $ex) {
                
                   ;
                    $id_tmp =null;
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
                            'name'=>$theme['name'],
                            'basePath' => '@tmp/' . $theme['name'],
                            'param' => $theme['param'],
                            'front'=>0,
                            'baseUrl' => '@web/admin/templates/' . $theme['name']
                ]);
               
            } else {
                throw new Exception(Text::_("LIB_FRAMEWORK_DOCUMENT_GETTEMPLATE"), 500);
            }
        }
        
        return $this->_theme;
    }

 
   

    public function dispatcher() {

        $this->loadSession();
        
        $this->loadUser();
  
        $widgets = RC::createObject(['class' => 'Widgets']);
      
        // перехватываем ошибки маршрутизатора
        try{
            $this->getRouter()->dispatcher();
            
            $id_exp = isset($this->getRouter()->exp->id_exp) ? $this->getRouter()->exp->id_exp : null;        
            $this->getView()->widgets = $widgets->getWidgets(null, $this->_styleId, $id_exp);
        } catch (\Exception $ex) {
            $this->getView()->widgets = $widgets->getWidgets(null, $this->_styleId, null);
        }
         
        $this->document->setHtmlClass(['admin',  $this->request->gerBrowser(), $this->request->getOS()]);
  
        $config = $this->getConfig();
        $this->document->set("robots", $config->meta_robots);
        $this->document->set("author", $config->meta_author);
        $this->document->set("type", "text/html");
        $this->document->set("charset", $config->charset);
        $this->document->set("language", $config->language);
        $this->document->set("title", $config->site_name);
        $this->document->set("favicon",$this->getTheme()->getUrl('favicon.png'));
        $menu = [];
        $expList = RC::getDb()->cache(function($db){ 
            return \maze\table\Expansion::find()->joinWith(['installApp'])
                ->from(['e'=>\maze\table\Expansion::tableName()])
                ->where(['ia.front_back'=>0])
                ->all();
        }, null, 'fw_system');
        
        foreach($expList as $exp){
           $menu[] = 
               [
                    'class' => 'ContextMenu',
                    "TITLE" => RC::getConf(array("type" => "expansion", "name" => $exp->name))->get('name'),
                    "HREF" => ['/admin/settings/expansion', ['run' => 'clear', 'id_exp' =>[$exp->id_exp], 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
           
        }
        $menu[] = [
                    'class' => 'ContextMenu',
                    "TITLE" => 'LIB_FRAMEWORK_APPLICATION_CLEARCACHETHUMB',
                    "HREF" => ['/admin/settings', ['run' => 'clearthumb', 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
        $menu[] = [
                    'class' => 'ContextMenu',
                    "TITLE" => 'LIB_FRAMEWORK_APPLICATION_CLEARCACHEASSETS',
                    "HREF" => ['/admin/settings', ['run' => 'clearassets', 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
        $this->getToolbar()->addGroup("system", [
            'class' => 'Buttonset',
            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLEARCACHE",
            "HREF" => ['/admin/settings', ['run' => 'clearcache', 'clear' => 'ajax']],
            "TYPE" => Buttonset::BTNBIG,
            "VISIBLE" => $this->getAccess()->roles("settings", "CLEAR_CACHE"),
            "SORT" => 2,
            "SORTGROUP" => 1,
            "SRC" => "/library/jquery/toolbarsite/images/big-refresh.png",
            "ACTION" => "$.get(this.href,$.noop, 'json');return false;",
            "MENU" =>$menu
           ]
        );
        
        if (!$this->getAccess()->isAdmin()) {
             $this->document->setHtmlClass('layout-logo');
             $this->document->setHtmlClass('access-nologin');
            RC::getErrorHandler()->errorLayout = "//logo";
            throw new maze\exception\UnauthorizedHttpException("Досутп запрещен");
        }
            
        $this->document->setHtmlClass('access-login');
        RC::getPlugin("system")->triggerHandler("beforeDispatcher");

        if(!isset($ex)){
            $this->getView()->component = $this->loadExp();
        }
        
         
        $this->document->setBobyClass(['component-'.$this->getRouter()->exp->name, 'controller-'.$this->getRouter()->controller]);
        if($this->getRouter()->view){
            $this->document->setBobyClass('view-'.$this->getRouter()->view);
        }
        if($this->getRouter()->layout){
            $this->document->setBobyClass('view-layout-'.$this->getRouter()->layout);
        }
        $this->document->setHtmlClass('theme-'.$this->getTheme()->name);
        
        if(isset($ex)){
            throw new maze\exception\NotFoundHttpException($exr->getMessage());
        }
        return $this;
       
    }

}

?>