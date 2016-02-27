<?php

namespace site\application;

use RC;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\helpers\StringHelper;
use maze\exception\UserException;

class View extends \maze\base\View {

    /**
     * позиция вывода <head>
     */
    const POSITION_HEADER = '{HEADER}';

  

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
     * @var string - контент компанента
     */
    public $component;

    protected $content;

    public function renderPage() {

        ob_start();
        ob_implicit_flush(false);

        $this->content = $this->render($this->layout);

        $this->renderComponentHtml();
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

    protected function renderComponentHtml() {
        $this->content = strtr($this->content, [self::POSITION_CONTENT => $this->component ]);
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
    
    public function isMessage() {
        return RC::app()->getSession()->get('message') ? true : false;
    }


}
