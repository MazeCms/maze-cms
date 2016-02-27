<?php defined('_CHECK_') or die("Access denied");?>
<?php 
$path = RC::app()->breadcrumbs;
$params = $this->getParams();

if(empty($path)) return false;

echo $this->render('tmp/default', ['path' => $path, 'params' => $params, 'id' => $this->id, 'widget' => $this]);
?>

