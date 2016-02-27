<?php


namespace wid\wid_toolbar\helpers;

use maze\helpers\Json;
use maze\helpers\Html;

class Menu
{
    public static function renderMenu($menu)
    {
         $count = 0;
        $html = '<ul style="display:none">';
        foreach ($menu as $item) {
            if(!$item->visible) continue;
            $icon = $item->src ? 'data-icon="' . $item->src . '"' : '';
            $action = $item->action ? Html::renderTagAttributes(['onclick'=>$item->action]) : '';
            if ($item->separator) {
                $html .= '<li><a data-type="siporator"></a>';
            } else {
                $href = 'href="' . ($item->href ? \Route::_($item->href) : 'javascript:void(0);') . '"';

                $html .= '<li><a data-type="link" ' . $action . ' ' . $href . ' ' . $icon . '>' . \Text::_($item->title) . '</a>';
            }
            if ($item->menu) {
                $html .= static::renderMenu($item->menu);
            }
            
            $html .=  '</li>';
            $count++;
        }
        $html .=  '</ul>';
        
        return $count > 0 ? $html : '';
    }
}
