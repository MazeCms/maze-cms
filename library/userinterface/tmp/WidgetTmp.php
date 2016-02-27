<?php

namespace ui\tmp;

use ui\select\Chosen;
use RC;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\helpers\FileHelper;
use maze\table\Template;

class WidgetTmp extends Chosen {

    public $front = 1;
    public $widgetName;

    public function init() {
        parent::init();
        $this->items = [];

        $root = '@root/';
        $rootTheme = '@root/';
        if ($this->front) {
            $root .= 'widgets/';
            $rootTheme .= 'templates/';
        } else {
            $root .= 'admin/widgets/';
            $rootTheme .= 'admin/templates/';
        }

        $path = RC::getAlias($root . 'wid_' . $this->widgetName . '/tmp');
        if (is_dir($path)) {
            $files = FileHelper::findFiles($path, ['only' => ['tmp.*']]);
            foreach ($files as $f) {
                $name = str_replace('tmp.', '', pathinfo($f, PATHINFO_FILENAME));
                $this->items[$this->widgetName][$name] = $name;
            }
        }


        $theme = Template::find()->where(['front' => $this->front])->all();

        foreach ($theme as $t) {
            $path = RC::getAlias($rootTheme . $t->name . '/views/widgets/wid_' . $this->widgetName . '/tmp');
            if (!is_dir($path))
                continue;
            $files = FileHelper::findFiles($path, ['only' => ['tmp.*']]);
            foreach ($files as $f) {
                $name = str_replace('tmp.', '', pathinfo($f, PATHINFO_FILENAME));
                $this->items[$t->name][$name] = $name;
            }
        }
    }

}
