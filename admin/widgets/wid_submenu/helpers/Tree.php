<?php


namespace wid\wid_submenu\helpers;

use maze\helpers\Json;

class Tree
{
    public static function renderMenu($items)
    {
     
         echo '<ul>';
            foreach($items as $item)
            {
                if(!$item['visible']) continue;                
                $data = [];
                $data['icon'] = $item['img'];
                $class = '';
                $help = false;
                if($item['active'])
                {                    
                    $data['opened'] = true;
                    $data['selected'] = true;
                }
                if(isset($item['help']))
                {
                    $help = $item['help'];
                }
                echo "<li id=\"".$item['id']."\" ".$class." data-jstree='".Json::encode($data, JSON_UNESCAPED_SLASHES)."'>";
                echo "<a ".(isset($item['onclick']) ? 'onclick="'.$item['onclick'].'"' : '')." ".($help ? 'title="'.$help.'"' : '')." href=\"".$item['path']."\">".$item['title']."</a>";
                    if(isset($item['item']))
                    {
                        static::renderMenu($item['item']);
                    }
                echo "</li>";
            }
        echo '</ul>';
    }
}
