<?php
defined('_CHECK_') or die("Access denied");

use maze\helpers\Json;

wid\wid_breadcrumbs\AssetWidget::register();

$options = [
    'tooltipClass' => 'dark-tooltip-bar',
    'show' => ['effect' => 'fade', 'delay' => 500, 'speed' => 100],
    'hide' => ['effect' => 'fade', 'delay' => 500],
    'position' => ['my' => 'left top', 'at' => 'left bottom+5']
];
$this->document->setTextScritp('$("#breadcrumbs").tooltip(' . Json::encode($options) . ');',['wrap'=>\Document::DOCREADY]);

$items =  [];
$items[] = ['label'=>'Домой', 'url'=>['/admin/']];
if(RC::app()->router->exp)
{
    $config =  RC::app()->router->exp->config;
    $title = '';
    if($config->get('description'))
    {
        $title .= '<div><strong>'.Text::_('WID_BREADCRUMBS_HOME').':</strong> '.$config->get('description').'</div>';
    }
    
    if($config->get('author'))
    {
        $title .= '<div><strong>'.Text::_('WID_BREADCRUMBS_AUTHOR').':</strong> '.$config->get('author').'</div>';
    }
    
    if($config->get('version'))
    {
        $title .= '<div><strong>'.Text::_('WID_BREADCRUMBS_VERSION').':</strong> '.$config->get('version').'</div>';
    }
    
    if($config->get('siteauthor'))
    {
        $title .= '<div><strong>'.Text::_('WID_BREADCRUMBS_SITE').':</strong> '.$config->get('siteauthor').'</div>';
    }
    
    $items[] = ['label'=>$config->get('name'), 'url'=>$config->getUrl(), 
        'options'=>['title'=>$title]
    ];    
}

if(RC::app()->breadcrumbs)
{
   $title =  array_slice(RC::app()->breadcrumbs,-1,1);
   if(isset($title[0]) && isset($title[0]['label']))
   {
       $this->document->set('title', Text::_($title[0]['label']));
   }
   
}
$items = array_merge($items, RC::app()->breadcrumbs);
echo admin\widgets\wid_breadcrumbs\ui\Breadcrumbs::element([
        'items'=>$items
    ]);
?>
