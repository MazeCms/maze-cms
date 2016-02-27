<?php defined('_CHECK_') or die("Access denied");

$model = RC::createObject(['class'=>'admin\expansion\exp_constructorblock\model\ModelBlock']);
$params = $this->getParams();

$contents = $model->getBlockByCode($params->getVar('block_id'));

if(!$contents) return false;

if (!is_array($contents)) {
   $contents = [$contents];
}
$result = null;
$themeName = RC::app()->theme->getName();
$view = RC::app()->view;
$layouts = [
    'default-'.$params->getVar('block_id').'-'.$this->id,
    'default-'.$params->getVar('block_id'),
    'default'
];
foreach ($layouts as $layout) {

    if ($view->hasView('@tmp/' . $themeName . '/views/widgets/wid_slidercontents/tmp/' . $layout)) {
        $result = $layout;
        break;
    } elseif ($view->hasView('/' . $layout)) {
        $result = $layout;
        break;
    }
}
 echo $this->render('tmp/'.$result, ['contents'=>$contents,'params'=>$params, 'id'=>$this->id, 'widget'=>$this]);


?>
