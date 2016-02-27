<?php

defined('_CHECK_') or die("Access denied");

use maze\helpers\Json;

wid\wid_maps\AssetWidget::register();

$params = $this->getParams();
$address = [];
if (!empty($params->getVar('country'))) {
    $address[] = $params->getVar('country');
}
if (!empty($params->getVar('region'))) {
    $address[] = $params->getVar('region');
}
if (!empty($params->getVar('city'))) {
    $address[] = $params->getVar('city');
}
if (!empty($params->getVar('street'))) {
    $address[] = $params->getVar('street');
}
if (!empty($params->getVar('house'))) {
    $address[] = $params->getVar('house');
}

$style = [];
if (!empty($params->getVar('width'))) {
    $style[] = 'width:' . $params->getVar('width');
}
if (!empty($params->getVar('height'))) {
    $style[] = 'height:' . $params->getVar('height');
}
$options = [
    'controls' => [
        'panControl' => $params->getVar('panControl'),
        'zoomControl' => $params->getVar('zoomControl'),
        'mapTypeControl' => $params->getVar('mapTypeControl'),
        'scaleControl' => $params->getVar('scaleControl'),
        'streetViewControl' => $params->getVar('streetViewControl'),
        'overviewMapControl' => $params->getVar('overviewMapControl')
    ],
    'maptype' => 'ROADMAP',
    'zoom' => $params->getVar('zoom'),
    'scrollwheel' => $params->getVar('scrollwheel'),
    'markers' => [
        [
            'address' => implode(',', $address),
            'html' => $params->getVar('textpopup'),
            'popup' => $params->getVar('popup')
        ]
    ]
];
$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "widget-form-send-" . $this->id);



$this->document->setTextScritp('$("#' . $id_css . '").gMap(' . Json::encode($options, JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE) . ');', ['wrap' => \Document::DOCREADY]);


echo $this->render('tmp/default', ['params' => $params, 'id_css' => $id_css, 'id' => $this->id, 'widget' => $this, 'style' => implode(';', $style)]);
?>
