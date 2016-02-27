<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-catalog-grid'],
            'colModel' => [
                 ["name" => "title", "title" => Text::_("EXP_CONTENTS_CATALOG_TITLE"), "index" => "ct.title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],                         
                ["name" => "description", "title" => Text::_("EXP_CONTENTS_CATALOG_DES"), "index" => "ct.description", "width" => 300, "left" => "center", "hidefild" => true],
                ["name" => "bundle", "title" => Text::_("EXP_CONTENTS_CATALOG_TYPE"), "index" => "ct.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true]
            ]
        ]);

$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>