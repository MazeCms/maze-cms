<?php defined('_CHECK_') or die("Access denied");
use wid\wid_menu\helpers\MenuHelper;


$menu = RC::getMenu();
$params = $this->getParams();

$items = $menu->getItemsByIDMenu($params->getVar('id_group'));
if(!$items) return false;

$this->panel->addButton(new Buttonset(array(
                            "TITLE" => "WID_MENU_PANEL_BTN_ITEMS",
                            "SORT" => 1,
                            "SRC" => "/library/jquery/toolbarsite/images/icon-menu-items.png",
                            "HREF" =>['/admin/menu/groupmenu', ['run'=>'menu', 'id_group'=>$params->getVar('id_group')]],
                            "ACTION" => "window.open(this.href); return false;"
                                )));

$itemsMenu = MenuHelper::getItems($items, $params, $this->id);
$layoutMenu = $params->getVar('layout') ? $params->getVar('layout') : 'default';

 echo $this->render('tmp/'.$layoutMenu, ['itemsMenu'=>$itemsMenu,'params'=>$params, 'id'=>$this->id, 'widget'=>$this]);
?>