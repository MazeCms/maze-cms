<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormsBuilder
 *
 * @author Nikolas-link
 */

namespace maze\toolbarsite;

use ui\Elements;
use maze\base\Model;
use maze\helpers\Html;
use maze\helpers\ArrayHelper;
use maze\base\JsExpression;
use maze\helpers\Json;
use RC;
use Text;
use Route;

class ToolbarBuilder extends Elements {

    /**
     * @var array options - настройки элемента id, class...
     */
    public $options;

    /**
     * @var array private - разрешения для редактирования 
     */
    public $private;

    /**
     *
     * @var array - массив кнопок
     */
    public $buttons;
    protected $buttonsEl;
    protected $access = false;

    public function init() {

        $access = RC::app()->access;

        if (!defined("EDITING_MODE") || !EDITING_MODE || !$access->roles("system", "VIEW_TOOLBAR")) {
            $this->access = false;
            return false;
        }

        if (is_array($this->private) && !empty($this->private)) {
            foreach ($this->private as $appname => $priv) {
                if (is_numeric($appname))
                    continue;

                if ($access->roles($appname, $priv)) {
                    $this->access = true;
                    break;
                }
            }
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = "element-settings-bar-wrapper-" . $this->getId();
        }
        $this->options['class'] = 'settings-panel-edits';

        if (!$this->access)
            return false;

        if (is_array($this->buttons) && !empty($this->buttons)) {
            foreach ($this->buttons as $btn) {
                $this->addButton($btn);
            }
        }
    }

    public function getStart() {
        $button = $this->getButton();
        ob_start();

        if (!empty($button)) {
            RC::app()->document->setTextScritp('$("#' . $this->options['id'] . '").toolBarElem()', ['wrap' => \Document::DOCREADY]);
            echo Html::beginTag('div', $this->options);

            echo "<ul>";

            for ($i = 0; $i < count($button); $i++) {
                if (!$button[$i]->visible) {
                    continue;
                }
                echo "<li>";
                $tooltip = $button[$i]->hint ? ' title="<div class=\'title-tooltip-bar\'>' . Text::_($button[$i]->hint["TITLE"]) . '</div>' . Text::_($button[$i]->hint["TEXT"]) . '"' : '';
                $options = [];
                if($button[$i]->src){
                   $options['data-icon'] = $button[$i]->src;
                } 
                if($button[$i]->icon){
                    $options['class'] = $button[$i]->icon;
                }
                if($button[$i]->action){
                    $options['onclick'] = $button[$i]->action;
                }
                
                $href = $button[$i]->href ? Route::_($button[$i]->href) : 'javascript:void(0);';
               
                
                echo Html::a(Text::_($button[$i]->title), $href, $options);

                if ($button[$i]->menu)
                    $this->context_menu($button[$i]->menu);

                echo "</li>";
            }

            echo "</ul>";
        }
    }

    public function run() {
        if (!empty($this->buttonsEl)) {
            echo Html::endTag('div');
        }

        return ob_get_clean();
    }

    public function addButton($button) {

        if ($this->access) {
            if (is_array($button)) {
                $button = RC::createObject($button);
            }

            if (is_object($button) && get_class($button) == "Buttonset") {
                if (!$button->sort) {
                    $button->sort = count($this->buttonsEl);
                }
                $this->buttonsEl[] = $button;
            }
        }
    }

    protected function getButton() {
        if (!empty($this->buttonsEl)) {
            usort($this->buttonsEl, function($a, $b) {
                if ($a->sort == $b->sort) {
                    return 0;
                }

                return ($a->sort > $b->sort) ? -1 : 1;
            });
        }
        return $this->buttonsEl;
    }

    protected function context_menu($menu) {
        echo '<ul style="display:none">';
        foreach ($menu as $item) {
            if (!$item->visible) {
                continue;
            }
            $options = [];
            if($item->src){
              $options['data-icon'] = $item->src;
            }
            if($item->action){
              $options['onclick'] = $item->action;
            }
           
            if ($item->separator) {
                echo '<li><a data-type="siporator"></a>';
            } else {
                $href = ($item->href ? Route::_($item->href) : 'javascript:void(0);');
                
                $options['data-type'] = "link";
                echo '<li>';
                echo Html::a(Text::_($item->title), $href, $options);
            }
            if (isset($menu->menu)) {
                $this->context_menu($menu->menu);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

}
