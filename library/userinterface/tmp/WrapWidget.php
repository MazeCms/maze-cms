<?php

namespace ui\tmp;

use ui\select\Chosen;
use RC;
use maze\helpers\ArrayHelper;
use maze\helpers\Html;
use maze\helpers\FileHelper;
use maze\table\Template;

class WrapWidget extends Chosen {

    public $front = 1;

    public function init() {
        parent::init();
        $this->items = [];

        $root = '@root/';
        if ($this->front) {
            $root .= 'templates/';
        }

        $files = FileHelper::findFiles(RC::getAlias($root . 'system/widgets/wrapper'), ['only' => ['tmp.*']]);
        foreach ($files as $f) {
            $name = str_replace('tmp.', '', pathinfo($f, PATHINFO_FILENAME));
            $this->items['system'][$name] = $name;
        }

        $theme = Template::find()->where(['front' => $this->front])->all();

        foreach ($theme as $t) {
            $files = FileHelper::findFiles(RC::getAlias($root . '/' . $t->name . '/views/widgets/wrapper'), ['only' => ['tmp.*']]);
            foreach ($files as $f) {
                $name = str_replace('tmp.', '', pathinfo($f, PATHINFO_FILENAME));
                $this->items[$t->name][$name] = $name;
            }
        }
    }

}
