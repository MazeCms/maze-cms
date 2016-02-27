<?php

use maze\base\JsExpression;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'dictionary-field-type-grid'],
            'colModel' => [
                ["name" => "bundle", "title" => Text::_("EXP_DICTIONARY_LABEL_CODETYPE"), "index" => "bundle", "width" => 100, "align" => "center",
                    "hidefild" => true, "sorttable" => true],
                ["name" => "title", "title" => Text::_("EXP_DICTIONARY_LABEL_TITLETYPE"), "index" => "title", "width" => 150, "align" => "left", "hidefild" => true, "sorttable" => true],
                ["name" => "description", "title" => Text::_("EXP_DICTIONARY_LABEL_DESTYPE"), "index" => "description", "width" => 300, "left" => "center", "hidefild" => true],
                ["name" => "countfield", "title" => Text::_("EXP_DICTIONARY_LABEL_COUNTFIELD"), "help" => Text::_("EXP_DICTIONARY_LABEL_COUNTFIELD"), "width" => 100, "align" => "center", "hidefild" => true]
            ]
        ]);


$grid->setPlugin("movesort", array(
    "sorttable" => false
));

ui\grid\MazeGrid::end();
?>