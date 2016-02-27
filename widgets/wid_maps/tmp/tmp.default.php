<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
$widget->panel->options['style'] = 'display: block;';
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<div <?= Html::renderTagAttributes(["class" => "wrapp-widget-maps wrapp-widget-map-id-$id " . $params->getVar("css_class"), 'id'=>$id_css, 'style'=>$style]) ?>>

</div>

