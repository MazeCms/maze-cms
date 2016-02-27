<?php

use maze\base\JsExpression;
use maze\helpers\Html;

$grid = ui\grid\MazeGrid::begin([
            'settings' => ['id' => 'role-grid'],
            'model' => 'maze\table\Roles',
            'colModel' => [
                ["name" => "id", "width" => 20, "title" => "Переключатель", 'visible' => $this->access->roles("user", "EDIT_ROLE")],
                ["name" => "menu", "width" => 20, "align" => "center", "title" => "Действия", 'visible' => $this->access->roles("user", "EDIT_ROLE")],
                ["name" => "name", "title" => Text::_("EXP_USER_ROLE_FORM_LABEL_NAME"), "index" => "name",
                    "hidefild" => true, "width" => 250, "align" => "left", "sorttable" => true, "grouping" => false],
                ["name" => "description", "title" => Text::_("EXP_USER_ROLE_FORM_LABEL_DES"),
                    "hidefild" => true, "width" => 250, "align" => "left", "sorttable" => false, "grouping" => false],
                ["name" => "private", "title" => Text::_("EXP_USER_ROLE_COUNTPRIVATE"),
                    "hidefild" => true, "width" => 80, "align" => "center", "sorttable" => false, "grouping" => false],
                ["name" => "id_role", "title" => "ID", "index" => "id_role", "hidefild" => true, "width" => 80,
                    "align" => "center", "sorttable" => true, "grouping" => false]
            ]
        ]);

$grid->setPlugin("checkbox", array(
    "fild" => "id",
    "name" => "id_role[]"
));

$grid->setPlugin("contextmenu", [
    "menu" => [
        "items" => ".menu-icon-handle",
        "data" => [
            ["type" => 'link', "spriteClass" => 'menu-icon-edits', "href" => Route::_([['run' => 'edit', 'id_role' => '{++id_role++}']]), "title" => Text::_("EXP_USER_TITLEMENU_EDIT")],
            ["type" => 'link', "spriteClass" => 'menu-icon-trash', "actions" => new JsExpression('cms.itemMenuDeletePromt'), "href" => Route::_([['run' => 'delete', 'id_role' => ['{++id_role++}']]]), "title" => Text::_("EXP_USER_TITLEMENU_DEL")],
        ]]
]);


$grid->setPlugin("movesort", [
    "sorttable" => false
]);

ui\grid\MazeGrid::end();
?>

