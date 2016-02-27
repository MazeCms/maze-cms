<?php

class Shortcode_Plugin_Content extends Plugin {

    protected $document;

    public function getArticleAfter(&$text) {

        $text = $this->getTextParseWidget($text);
    }

    protected function getTextParseWidget($buffer) {
        $pattern = "#{SHORTCODE[\s]*position=\"(.+)\"[\s]*}#isU";
        
        $search = [];
        $replace = [];
        preg_match_all($pattern, $buffer, $arr, PREG_SET_ORDER);
        
        foreach ($arr as $widget) {
            $position = (isset($widget[1]) && !empty($widget[1])) ? $widget[1] : false;

            if ($position) {
                $getWid = $this->renderWidget($position);
            }

            array_push($search, "#" . $widget[0] . "#is");
            array_push($replace, $getWid);
        }
        
        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }
    
    protected function renderWidget($position) {
        $result = "";
        
        $widgets = $this->loadWidget();
        if(!isset($widgets[$position]))
            return $result;

        foreach ($widgets[$position] as $widget) {            
            
            try {
                $id_wid = $widget['id_wid'];

                $panel = null;
                if (defined("SITE")) {
                    $panel = \maze\toolbarsite\ToolbarBuilder::begin([
                        "options"=>['id'=>"admin-edits-settings-panel-" . $id_wid],
                        "private"=>array("widget" => "EDIT_WIDGET"),
                        "buttons"=> [
                        new Buttonset(array(
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_EDIT_ONER",
                            "SORT" => 3,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-edit.png",
                            "HREF" =>['/admin/widget', ['run'=>'edit', 'id_wid'=>$id_wid,'front'=>1, 'clear'=>'ajax']],
                            "ACTION" => "cms.formDialogSave(this,{title:'".Text::_("LIB_USERINTERFACE_TOOLBAR_EDIT_NAME",['name'=>$widget['title'] ])."'});return false;",
                            "MENU" => array(
                                new ContextMenu(array(
                                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_NEW",
                                    "SORT" => 1,
                                    "HREF" => "/admin/widget/?run=edit&id_wid=" . $id_wid . "&front=1",
                                    "ACTION" => "window.open(this.href); return false;"
                                        )),
                                new ContextMenu(array(
                                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                                    "SORT" => 1,
                                    "HREF" => "/admin/widget/?run=edit&id_wid=" . $id_wid . "&front=1"
                                        ))
                            )
                                )),
                        new Buttonset(array(
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_DELET_BUTTON",
                            "SORT" => 1,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-trash.png",
                            "HREF" =>['/admin/widget', ['run'=>'delete', 'id_wid'=>[$id_wid],'front'=>1]],
                            "ACTION" => "cms.deleteBlock(this, '#admin-edits-settings-panel-" . $id_wid."'); return false;"
                                )),
                        new Buttonset(array(
                            "TITLE" => "LIB_USERINTERFACE_TOOLBAR_UNPUBLISH_BUTTON",
                            "SORT" => 2,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-close.png",
                            "HREF" =>['/admin/widget', ['run'=>'unpublish', 'id_wid'=>[$id_wid],'front'=>1]],
                            "ACTION" => "cms.deleteBlock(this, '#admin-edits-settings-panel-" . $id_wid."');return false;"
                                ))
                            ]
                    ]
                            
                    );
                }
                $widgetObject = RC::createObject([
                            'class' => 'maze\widgets\Widget',
                            'name' => $widget['name'],
                            'time_cache' => $widget['time_cache'],
                            'enable_cache' => $widget['enable_cache'],
                            'position' => $position,
                            'id' => $widget['id_wid'],
                            'param' => $widget['param'],
                            'panel' => $panel
                ]);

                if (!$widgetObject->isWidget()) {
                    throw new UserException(Text::_("LIB_FRAMEWORK_DOCUMENT_GETWIDGET", array($widgetObject->getPath())), 300);
                    continue;
                }

                $body = $widgetObject->run();

                if (defined("SITE")) {
                    $panel->getStart();
                    echo $body;
                    $result .= $panel->run();
                } else {
                    $result .= $body;
                }
            } catch (\Exception $exp) {
                RC::getLog()->add('error', [
                    'file' => $exp->getFile(),
                    'line' => $exp->getLine(),
                    'code' => $exp->getCode(),
                    'message' => $exp->getMessage(),
                    'category' =>get_class($exp)]);
            }
        }

        return $result;
    }

    public function loadWidget() {
        static $widgets;
        if ($widgets !== null)
            return $widgets;
        
        $widget = RC::createObject(['class' => 'Widgets']);
        
        $router = RC::app()->router;
        $id_menu = isset($router->menu->id_menu) ? $router->menu->id_menu : null;
        


        return $widgets = $widget->getShortWidgets($id_menu, $router->exp->id_exp);
    }

}

?>