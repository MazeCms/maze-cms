<?php

defined('_CHECK_') or die("Access denied");

use maze\table\InstallApp;

$asset = new \ui\assets\AssetExp([
    'basePath' => '@admin/expansion/exp_templating/assets'
        ]);
$urlAsset = $asset->getAssetBaseUrl();

$model = RC::createObject(['class' => 'admin\expansion\exp_constructorblock\model\ModelBlock']);
$params = $this->getParams();
$theme = RC::app()->theme;
$path = $theme->basePath;



$contents = $model->getBlockByCode($params->getVar('block_id'), $params->getVar('phpcode'));

if (!$contents)
    return false;

if (!is_array($contents)) {
    $contents = [$contents];
}

$result = null;
$themeName = RC::app()->theme->getName();
$view = RC::app()->view;
$layouts = [
    'default-' . $params->getVar('block_id') . '-' . $this->id,
    'default-' . $params->getVar('block_id'),
    'default'
];

foreach ($layouts as $layout) {

    if ($view->hasView('@tmp/' . $themeName . '/views/widgets/wid_block/tmp/' . $layout)) {
        $result = $layout;
        break;
    } elseif ($view->hasView('/' . $layout)) {
        $result = $layout;
        break;
    }
}
if(!empty($params->getVar('layout'))){
    $result = $params->getVar('layout');
}

if ($params->getVar('block_id') && RC::app()->access->roles("templating", "EDIT_TMP") && $result != "default") {
    $template = RC::getDb()->cache(function($db) use ($theme) {
        return InstallApp::find()->where(['type' => 'template', 'name' => $theme->name])->one();
    }, null, 'exp_templating');
    $this->panel->addButton((new Buttonset([
        "TITLE" => "WID_BLOCK_PARAMS_VIEWBLOCK",
        "SORT" => 1,
        "VISIBLE" => RC::app()->access->roles("templating", "EDIT_TMP"),
        "SRC" => $urlAsset . "/img/icon-html.png",
        "MENU" => [
            new ContextMenu(array(
                "TITLE" => "WID_BLOCK_PARAMS_TEMPLATE",
                "SORT" => 1,
                "ACTION" => "file_edit_template('php', '" . str_replace([$theme->basePath, "\\"], ["", "/"], RC::getAlias('@tmp/' . $themeName . '/views/widgets/wid_block/tmp/')) . "tmp.$result.php', '(" . $result . ")', " . $template->id_app . "); return false;"
                    )),
            new ContextMenu(array(
                "TITLE" => "WID_BLOCK_PARAMS_CONSTRUCTBLOCK",
                "SORT" => 1,
                "HREF" => ["/admin/constructorblock", ["run"=>"edit", "code"=>$params->getVar('block_id'), "return"=>URI::current()]],
                "ACTION" => "window.open(this.href); return false;"
                    ))
        ]
    ])));
}


echo $this->render('tmp/' . $result, ['contents' => $contents, 'params' => $params, 'id' => $this->id, 'widget' => $this]);
?>
