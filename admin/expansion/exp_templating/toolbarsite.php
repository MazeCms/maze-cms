<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\FileHelper;
use maze\table\InstallApp;

$doc = RC::app()->document;
ui\assets\AssetCodeMirror::register();

$doc->setLangTextScritp(array(
    "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_CLOSE",
    "LIB_USERINTERFACE_TOOLBAR_EDIT_BUTTON",
    "EXP_TEMPLATING_PANELSITE_MESS_SAVE"
));
$asset = new \ui\assets\AssetExp([
   'basePath'=>'@admin/expansion/exp_templating/assets'
]);
$urlAsset = $asset->getAssetBaseUrl();

$doc->addScript($urlAsset."/js/templatesite.js");

$menu_css = [];
$theme = RC::app()->theme;
$template = RC::getDb()->cache(function($db) use ($theme) {
    return InstallApp::find()->where(['type' => 'template', 'name' => $theme->name])->one();
}, null, 'exp_templating');

if ($theme && $template) {
    $file_css = FileHelper::findFiles($theme->basePath, ['only'=>['*.css']]);
    for ($i = 0; $i < count($file_css); $i++) {
        $menu_css[] = [
            "class" => 'ContextMenu',
            "TITLE" => Text::_("EXP_TEMPLATING_PANELSITE_STYLESHETS", ['name'=>basename($file_css[$i])]),
            "ACTION" => "file_edit_template('css', '" . str_replace([$theme->basePath, "\\"], ["","/"], $file_css[$i]) . "', '" . basename($file_css[$i]) . "', ".$template->id_app."); return false;"
        ];
    }
}


RC::app()->getToolbar()->addGroup("template", new Buttonset(array(
    "TITLE" => "EXP_TEMPLATING_PANELSITE_STYLEEDIT",
    "TYPE" => "MIN",
    "SORT" => 10,
    "SORTGROUP" => 2,
    "VISIBLE" =>$this->access->roles("templating", "EDIT_STYLE") && $this->access->roles("templating", "EDIT_TMP"),
    "HINT" => array("TITLE" => "EXP_TEMPLATING_PANELSITE_STYLEEDIT", "TEXT" => "EXP_TEMPLATING_PANELSITE_STYLEEDITDES"),
    "SRC" => $urlAsset."/img/icon-cssthree.png",
    "MENU" => $menu_css
        ))
);
$menu_temp = array();
if ($theme && $template) {
    $path = $theme->basePath;
    if (file_exists($path . DS . "tmp.index.php")) {
        $menu_temp[] = new ContextMenu(array(
            "TITLE" => "EXP_TEMPLATING_PANELSITE_TMP_SITE",
            "SORT" => 10,
            "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["","/"], $path) . "/tmp.index.php', '" . Text::_("EXP_TEMPLATING_PANELSITE_TMP_SITE") . "', ".$template->id_app."); return false;"
        ));
    }
    if (file_exists($path . DS . "tmp.error.php")) {
        $menu_temp[] = new ContextMenu(array(
            "TITLE" => "EXP_TEMPLATING_PANELSITE_TMP_ERROR",
            "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["","/"], $path) . "/tmp.error.php', '" . Text::_("EXP_TEMPLATING_PANELSITE_TMP_ERROR") . "', ".$template->id_app."); return false;"
        ));
    }
    if (file_exists($path . DS . "tmp.disable.php")) {
        $menu_temp[] = new ContextMenu(array(
            "TITLE" => "EXP_TEMPLATING_PANELSITE_TMP_DISABLE",
            "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["","/"], $path) . "/tmp.disable.php', '" . Text::_("EXP_TEMPLATING_PANELSITE_TMP_DISABLE") . "', ".$template->id_app."); return false;"
        ));
    }
}


RC::app()->getToolbar()->addGroup("template", new Buttonset(array(
    "TITLE" => "EXP_TEMPLATING_PANELSITE_TMP_EDIT",
    "TYPE" => "MIN",
    "SORT" => 9,
    "VISIBLE" =>$this->access->roles("templating", "EDIT_TMP"),
    "SORTGROUP" => 2,
    "HINT" => array("TITLE" => "EXP_TEMPLATING_PANELSITE_TMP_EDIT", "TEXT" => "EXP_TEMPLATING_PANELSITE_TMP_EDITDES"),
    "SRC" => $urlAsset."/img/icon-html.png",
    "MENU" => $menu_temp
        ))
);

$url = new URI(URI::instance()->toString(array('path', 'query', 'fragment')));
$viewPos = $url->hasVar("wid_view");
if ($viewPos) {
    $url->delVar("wid_view");
} else {
    $url->setVar("wid_view", 1);
}

RC::app()->getToolbar()->addGroup("template", new Buttonset(array(
    "TITLE" => $viewPos ? "EXP_TEMPLATING_PANELSITE_TMP_CLOSEVIWEPOS" : "EXP_TEMPLATING_PANELSITE_TMP_VIWEPOS",
    "TYPE" => "MIN",
    "SORT" => 8,
    "SORTGROUP" => 2,
    "HREF" => $url->toString(array('path', 'query', 'fragment')),
    "HINT" => array("TITLE" => "EXP_TEMPLATING_PANELSITE_POSITION", "TEXT" => "EXP_TEMPLATING_PANELSITE_POSITION_DES"),
    "SRC" => $urlAsset."/img/" . ($viewPos ? "icon-eye-close.png" : "icon-eye-open.png"),
    "ACTION" => "document.location = this.href; return false;"
        ))
);
?>