<?php

use maze\helpers\Html;
?>

<?php

echo Html::beginTag('ul', ['class' => 'menu-level-' . $itemsMenu[0]['level']]);
foreach ($itemsMenu as $item) {

    echo Html::beginTag('li', $item['attr_li']);
    if(trim($item['item']->get('menu_attr_rel'))){
        $item['attr_a']['rel'] =   $item['item']->get('menu_attr_rel');    
    }
    if(trim($item['item']->get('menu_attr_onclick'))){
        $url = "#";
        $item['attr_a']['data-url'] =  Route::_($item['item']->path);
        $item['attr_a']['onclick'] = $item['item']->get('menu_attr_onclick');
    }else{
        $url = $item['item']->path;
    }
    echo Html::a($item['item']->name, $url, $item['attr_a']);
    if (isset($item['items']) && $item['items']) {

        echo $this->render('tmp/default-item', ['itemsMenu' => $item['items'], 'widget' => $widget], $widget);
    }
    echo Html::endTag('li');
}
echo Html::endTag('ul');
?>


