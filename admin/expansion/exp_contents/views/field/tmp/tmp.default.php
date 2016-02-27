<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'contents-field-type-grid'],
            'colModel' => [
                ["name" => "bundle", "title" => Text::_("EXP_CONTENTS_LABEL_CODETYPE"), "index" => "ct.bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_CONTENTS_LABEL_TITLETYPE"), "index" => "ct.title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_CONTENTS_LABEL_DESTYPE"), "index" => "ct.description", "width" => 250, "align" => "left", "hidefild" => true],
                ["name" => "countfield", "title" => Text::_("EXP_CONTENTS_LABEL_COUNTFIELD"), "help" => Text::_("EXP_CONTENTS_LABEL_COUNTFIELD"), "width" => 100, "align" => "center", "hidefild" => true]
            ]
        ]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>