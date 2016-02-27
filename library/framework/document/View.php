<?php

namespace maze\document;

use RC;
use Text;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use ToolBarelem;
use ContextMenu;
use Buttonset;
use maze\helpers\StringHelper;
use maze\exception\UserException;
use maze\document\Block;
use URI;

class View extends \maze\base\View {

    /**
     * позиция вывода <head>
     */
    const POSITION_HEADER = '{HEADER}';

    /**
     * позиция вывода панели инстурментов
     */
    const POSITION_TOOLBARPANEL = '{TOOLBARPANEL}';

    /**
     * позиция вывода сообщений сайта
     */
    const POSITION_MESSAGE = '{MESSAGE}';

    /**
     * позиция вывода контента
     */
    const POSITION_CONTENT = '{CONTENT}';

    /**
     * @var string - шаблон сайта 
     */
    public $layout = '//index';

    /**
     * @var array - виджеты сайта вида [позиция=>[...]]
     */
    public $widgets;

    /**
     * @var string - контент компанента
     */
    public $component;

    /**
     * @var string - html панели инструментов
     */
    public $toolbar;
    
    protected $content;
    /**
     * @var array - колекция блоков
     */
    public $blocks;

    public function renderPage() {

        ob_start();
        ob_implicit_flush(false);

        $this->content = $this->render($this->layout);

        $this->renderComponentHtml();

        $this->renderWidgetsHtml();
        $this->renderToolBarHtml();
        $this->renderMessageHtml();
        $this->renderHeadHtml();


        return ob_get_clean() . $this->content;
    }

    public function renderPageJson() {
        $this->content = [];
        $this->renderComponentJson();
        $this->renderHeadJson();
        $this->renderMessageJson();
        return $this->content;
    }

    public function getDocument() {
        return RC::app()->document;
    }

    public function getAccess() {
        return RC::app()->access;
    }

    public function registerAssetBundle($class, array $args = []) {
        RC::app()->document->registerAssetBundle($class, $args);
    }

    public function registerMetaTag($options, $key = null) {
        
    }

    public function setTextScritp($text, $options = []) {
        RC::app()->document->setTextScritp($text, $options);
    }

    public function addStylesheet($url, $options = []) {
        RC::app()->document->addStylesheet($url, $options);
    }

    public function setLangTextScritp($lang, $handler = "cms.setLang") {
        RC::app()->document->setLangTextScritp($lang, $handler);
    }

    public function addScript($url, $options = []) {
        RC::app()->document->addScript($url, $options);
    }

    protected function renderWidget($position, $wrapper = "demo") {
        $result = "";
        if (!$this->isWidget($position))
            return false;

        
        if (!isset($this->widgets[$position]) && $this->isViewPosition()) {

            $result = $this->render('@tmp/system/widgets/wrapper/' . $wrapper, [
                "title" => Text::_("LIB_FRAMEWORK_DOCUMENT_WIDGET_TITLE"),
                "id_wid" => 0,
                "title_show" => 1,
                "position" => $position,
                "body" => ""
            ]);

            return $this->rederMarkerWidget($result, $position, $wrapper);
        }


        foreach ($this->widgets[$position] as $widget) {
            
            
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
                                    "HREF" => ['/admin/widget', ['run'=>'edit', 'id_wid'=>$id_wid,'front'=>1, "return"=>URI::current()]],
                                    "ACTION" => "window.open(this.href); return false;"
                                        )),
                                new ContextMenu(array(
                                    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_WINDOW_SELF",
                                    "SORT" => 1,
                                    "HREF" => ['/admin/widget', ['run'=>'edit', 'id_wid'=>$id_wid,'front'=>1, "return"=>URI::current()]]
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
                $param = unserialize($widget['param']);
                
                if(isset($param['wrapper']) && !empty(trim($param['wrapper']))){
                    $wrapper = trim($param['wrapper']);
                }
                $widgetWrapp = $this->render('@tmp/system/widgets/wrapper/' . $wrapper, [
                    "title" => $widget['title'],
                    "id_wid" => $widget['id_wid'],
                    "position" => $position,
                    "title_show" => $widget['title_show'],
                    "body" => $body
                ]);


                if (defined("SITE")) {
                    $panel->getStart();
                    echo $widgetWrapp;
                    $result .= $panel->run();
                } else {
                    $result .= $widgetWrapp;
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
        if ($this->isViewPosition()) {
           
            return $this->rederMarkerWidget($result, $position, $wrapper);
        }
        return $result;
    }

    protected function rederMarkerWidget($content, $position, $wrap) {

        $html = "<div class=\"marker-widget\">";
        $html .="<div class=\"marker-widget-title\">";
        $html .= "<div><strong>" . Text::_("LIB_FRAMEWORK_DOCUMENT_WIDGET_POSITINO") . "</strong>: " . $position . "</div>";
        $html .= "<div><strong>" . Text::_("LIB_FRAMEWORK_DOCUMENT_WIDGET_WRAPER") . "</strong>: " . $wrap . "</div>";
        $html .= "<div><strong>" . Text::_("LIB_FRAMEWORK_DOCUMENT_TEG") . "</strong>: {WIDGET}</div>";
        $html .="</div>";
        $html .= $content;
        $html .="</div>";

        return $html;
    }

    protected function renderWidgetsHtml() {
        $pattern = "#{WIDGET[\s]*position=\"(.+)\"([\s]*wrapper=\"(.+)\")?[\s]*}#isU";
        $search = [];
        $replace = [];
        preg_match_all($pattern, $this->content, $arr, PREG_SET_ORDER);


        foreach ($arr as $widget) {
            $position = (isset($widget[1]) && !empty($widget[1])) ? $widget[1] : false;

            $wrapper = (isset($widget[3]) && !empty($widget[3])) ? $widget[3] : "demo";

            ;
            if ($position) {

                $getWid = $this->renderWidget($position, $wrapper);
            }


            array_push($search, "#" . $widget[0] . "#is");
            array_push($replace, $getWid);
        }

        $this->content = preg_replace($search, $replace, $this->content);
    }

    protected function renderComponentHtml() {


        if ($this->isViewPosition()) {
            $title = "<div class=\"marker-widget-title\">";
            $title .= "<div><strong>" . Text::_("LIB_FRAMEWORK_DOCUMENT_CONTENT_TITLE") . "</strong></div>";
            $title .= "<div><strong>" . Text::_("LIB_FRAMEWORK_DOCUMENT_TEG") . "</strong>: {CONTENT}</div>";
            $title .= "</div>";
            $html = "<div class=\"marker-widget\">" . $title . $this->component . "</div>";
        } else {
            $html = $this->component;
        }


        $this->content = strtr($this->content, [self::POSITION_CONTENT => $html]);
    }

    protected function renderComponentJson() {
        if (RC::app()->response->data !== null) {
            $this->component = RC::app()->response->data;
        }

        if (StringHelper::isStrJSON($this->component)) {
            $this->content = json_decode($this->component, true);
        } else {
            $this->content['html'] = $this->component;
        }
    }

    protected function renderHeadHtml() {
        $doc = RC::app()->document;

        $html = '';
        ob_start();

        $html .= $doc->getFavicon();

         foreach ($doc->getLink() as $link)
            $html .= $link;
         
        foreach ($doc->getMeta() as $meta)
            $html .= $meta;

        foreach ($doc->getStylesheet() as $style)
            $html .= $style;

        foreach ($doc->getSctipts() as $script)
            $html .= $script;

        $html .= $doc->getTextScritp();
        $html .= $doc->getTextCss();
        $html .= $doc->getTitle();
        $html = ob_get_clean() . $html;

        $this->content = strtr($this->content, [self::POSITION_HEADER => $html]);
    }

    protected function renderHeadJson() {
        $doc = RC::app()->document;

        $this->content["head"]["stylesheet"] = $doc->getObjStylesheet();
        $this->content["head"]["script"] = $doc->getObjSctipts();
        $this->content["head"]["textcss"] = $doc->getObjTextCss();
        $this->content["head"]["textscript"] = $doc->getObjTextScritp();
        $this->content["head"]["title"] = $doc->get('title');
        return $this->content;
    }

    protected function renderToolBarHtml() {
        $this->content = strtr($this->content, [self::POSITION_TOOLBARPANEL => $this->toolbar]);
    }

    protected function renderMessageHtml() {
        $html = '';
        if ($mess = RC::app()->document->getMessage()) {
            $html = $this->render('@tmp/system/message/' . $mess['type'], ['text' => $mess['text']]);
            $html = '<div id="system-labyrinth-message">' . $html . '</div>';
        }

        $this->content = strtr($this->content, [self::POSITION_MESSAGE => $html]);
    }

    protected function renderMessageJson() {
        $this->content['message'] = RC::app()->document->getMessage();
    }

    public function isViewPosition() {
        if (RC::app()->config->viewposition && RC::app()->getRequest()->get("wid_view") && $this->getAccess()->roles("system", "VIEW_POSITION")) {
            return true;
        } else {
            return false;
        }
    }

    public function isWidget($position) {
        if ($this->isViewPosition())
            return true;

        return !empty($this->widgets[$position]) ? true : false;
    }

    public function isMessage() {
        return RC::app()->getSession()->get('message') ? true : false;
    }
    
    public function beginBlock($id, $renderInPlace = false)
    {
        return Block::begin([
            'id' => $id,
            'renderInPlace' => $renderInPlace,
            'view' => $this,
        ]);
    }

    public function endBlock()
    {
        Block::end();
    }
    
    public static function getWidgetsPosiotion($tmp_name, $front) {
        $position_name = [];

        $root = $front ? PATH_ROOT : PATH_SITE;

        $path = $root . DS . "templates" . DS . $tmp_name;

        $layout = scandir($path);
        if (!$layout) {
            $layout = ["index", "logo", "error", "disable"];
        } else {
            $layout = array_map(function($file) {
                return preg_match("/tmp\.([a-z-0-9._-]+)\.php/i", $file, $matches) ? $matches[1] : null;
            }, $layout);
        }
        foreach ($layout as $file) {
            $layot_file = $path . DS . "tmp." . $file . ".php";

            if (!file_exists($layot_file))
                continue;

            $buffer = file_get_contents($layot_file);

            $pattern = "#{WIDGET[\s]*position=\"(.+)\"([\s]*wrapper=\"(.+)\")?[\s]*}#isU";

            preg_match_all($pattern, $buffer, $arr, PREG_SET_ORDER);

            foreach ($arr as $widget) {
                $position = (isset($widget[1]) && !empty($widget[1])) ? $widget[1] : false;

                if ($position) {
                    if (!in_array($position, $position_name)) {
                        array_push($position_name, $position);
                    }
                }
            }
        }

        return $position_name;
    }

}
