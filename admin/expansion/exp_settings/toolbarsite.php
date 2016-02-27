<?php

defined('_CHECK_') or die("Access denied");
$menu = [];
        $expList = RC::getDb()->cache(function($db){ 
            return \maze\table\Expansion::find()->joinWith(['installApp'])
                ->from(['e'=>\maze\table\Expansion::tableName()])
                ->where(['ia.front_back'=>0])
                ->all();
        }, null, 'fw_system');
foreach($expList as $exp){
           $menu[] = 
               [
                    'class' => 'ContextMenu',
                    "TITLE" => RC::getConf(array("type" => "expansion", "name" => $exp->name))->get('name'),
                    "HREF" => ['/admin/settings/expansion', ['run' => 'clear', 'id_exp' =>[$exp->id_exp], 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
           
        }
        $menu[] = [
                    'class' => 'ContextMenu',
                    "TITLE" => 'LIB_FRAMEWORK_APPLICATION_CLEARCACHETHUMB',
                    "HREF" => ['/admin/settings', ['run' => 'clearthumb', 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
        $menu[] = [
                    'class' => 'ContextMenu',
                    "TITLE" => 'LIB_FRAMEWORK_APPLICATION_CLEARCACHEASSETS',
                    "HREF" => ['/admin/settings', ['run' => 'clearassets', 'clear' => 'ajax']],
                    "ACTION" => "$.get(this.href,$.noop, 'json');return false;"
                ];
RC::app()->getToolbar()->addGroup("system", new Buttonset([
    "TITLE" => "LIB_USERINTERFACE_TOOLBAR_CLEARCACHE",
    "TYPE" => "BIG",
    "HREF" => ['/admin/settings',['run' => 'clearcache', 'clear'=>'ajax']],
    "SORT" => 1,
    "SORTGROUP" => 10,
    "SRC" => "/library/jquery/toolbarsite/images/big-refresh.png",
    "ACTION" => "$.get(this.href,$.noop, 'json');return false;",
    "MENU" =>$menu
    ])
);
?>