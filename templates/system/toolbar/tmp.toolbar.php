<?php
$this->registerAssetBundle('ui\assets\AssetStyleCore');
$this->registerAssetBundle('ui\assets\AssetCore');
$this->registerAssetBundle('ui\assets\AssetToolBarsite');
$this->addScript("/library/jquery/cms/jquery.admin.js");

$this->setTextScritp('jQuery(document).ready(function(){
     $("#tool-bar-site").toolBarSite();
     jQuery(document).bind("ajaxSuccess", function(e, xhr, res, data){
        if(typeof data == "object"){
            if(data.hasOwnProperty("message") && data.message){
                if($("#tool-bar-site").is("#tool-bar-site")){
                    $("#tool-bar-site").toolBarSite("setMessage", data.message.text, data.message.type);
                }                        
            }
        }
    });
     $("#tool-bar-site input[name=editing_mode]").mazeSwitch().bind("inchange.mazeSwitch unchange.mazeSwitch", function(e){
                    var mode = e.type == "inchange" ? 1 : 0;			
                    var url = cms.URI();
                    url.setVar("editing_mode", mode);
                    document.location = url.toString();
            });	 
})');

$this->setLangTextScritp(array(
    "LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_SAVECLOSE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_CLOSE",
    "LIB_USERINTERFACE_TOOLBAR_PACK_SEND",
    "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE",
    "LIB_USERINTERFACE_TOOLBAR_UPDATEPAGE",
    "LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT",
    "LIB_FRAMEWORK_VIEW_AJAX_ERROR",
    "LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR",
    "LIB_FRAMEWORK_VIEW_AJAX_REDIRECT",
    "LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TITLE",
    "LIB_USERINTERFACE_TOOLBAR_CLOSE_BUTTON",
    "LIB_USERINTERFACE_TOOLBAR_CLOSE",
    "LIB_USERINTERFACE_FIELD_LOGIN",
    "LIB_USERINTERFACE_FIELD_PASS",
    "LIB_USERINTERFACE_TOOLBAR_ALERTPROMT_MESS_TEXT",
    "LIB_USERINTERFACE_TOOLBAR_PACK_SEND",
    "LIB_FRAMEWORK_VIEW_AJAX_AUTHORIZATION_TITLE",
    "LIB_USERINTERFACE_TOOLBAR_ALERT_MESS_TEXT",
    "LIB_USERINTERFACE_FIELD_ADDIMAGES_DIALOG"
));
$access = RC::app()->getAccess();
$user = $access->get();

function recursive_menu($items) {

    $visible = 0;
    $html = '<ul style="display:none">';
    foreach ($items as $item) {
        if (!$item['visible'])
            continue;
        $html .= '<li><a data-type="link" data-icon="' . $item["img"] . '" href="' . ($item["path"] ? Route::_($item["path"]) : 'javascript:void(0)') . '">' . Text::_($item["title"]) . '</a>';
        if (isset($item['item'])) {
            $html .= recursive_menu($item['item']);
        }
        $html .= '</li>';
        $visible++;
    }
    $html .= '</ul>';

    return $visible > 0 ? $html : '';
}

function context_menu($menu) {
    echo '<ul style="display:none">';
    foreach ($menu as $item) {
        $icon = $item->src ? 'data-icon="' . $item->src . '"' : '';
        $action = $item->action ? 'onclick="' . $item->action . '"' : '';
        if ($item->separator) {
            echo '<li><a data-type="siporator"></a>';
        } else {
            $href = 'href="' . ($item->href ? Route::_($item->href) : 'javascript:void(0);') . '"';

            echo '<li><a data-type="link" ' . $action . ' ' . $href . ' ' . $icon . '>' . Text::_($item->title) . '</a>';
        }
        if ($item->menu) {
            context_menu($item->menu);
        }
        echo '</li>';
    }
    echo '</ul>';
}
?>

<div id="tool-bar-site" class="tool-bar-site">
    <div class="tbs-top-tools">
        <div class="tbs-top-left">
            <a id="tbs-top-appmenu" class="btn-top-bar"><span class="icon-top-bar bar-icon-menu"></span><?php echo Text::_("LIB_USERINTERFACE_FIELD_MENU") ?> </a>
            <?= recursive_menu(RC::app()->menu->createMenu()); ?>
        </div>
        <div class="tbs-top-center">
            <a id="tabs-site-topbar" href="javascript:void(0);"class="btn-top-bar tabs-bar active"><?php echo Text::_("LIB_USERINTERFACE_FIELD_SITE") ?></a>
            <a id="tabs-admin-topbar" href="/admin"  class="btn-top-bar tabs-bar"><?php echo Text::_("LIB_USERINTERFACE_FIELD_ADMIN") ?></a>
        </div>
        <div class="tbs-top-right">
            <?php if(RC::app()->access->roles("user", "EDIT_SELF_USER") && $user['id_user'] == RC::app()->access->getUid() ):?>
            <a class="btn-top-bar active" href="/admin/user/?run=edit&id_user=<?php echo $user["id_user"] ?>"><span class="icon-top-bar bar-icon-user"></span><?php echo $user["username"] ?></a>
            <?php else:?>
            <a class="btn-top-bar active" href="#"><span class="icon-top-bar bar-icon-user"></span><?php echo $user["username"] ?></a>
            <?php endif;?>            
            <a class="btn-top-bar" href="/user/?run=logout"><?php echo Text::_("LIB_USERINTERFACE_FIELD_USEROUT"); ?></a>
            <a class="btn-top-bar tbs-bottom-switch hide-block-top" href="javascript:void(0);"><span class="icon-top-bar bar-icon-down"></span> <?php echo Text::_("LIB_USERINTERFACE_FIELD_UPPANEL"); ?></a>
            <a id="fixed-top-bar" class="btn-top-bar" href="javascript:void(0);"><span class="icon-top-bar bar-icon-clip"></span></a>
        </div>
    </div>
    <div class="tbs-bottom-tools">
        <div class="tbs-siporator-duble"></div>
        <div class="tbs-panel-buttons">

            <?php
            $groups = RC::app()->toolbar->group;
            $i = 0;
            foreach ($groups as $group):
                ?>
                <div class="tbs-bottom-group">      	
                    <?php
                    $countMin = 0;
                    foreach ($group as $btn) {
                        if (!$btn->visible)
                            continue;
                        $tooltip = $btn->hint ? ' title="<div class=\'btn-tooltip-bar\'>' . Text::_($btn->hint["TITLE"]) . '</div>' . Text::_($btn->hint["TEXT"]) . '"' : '';
                        $icon = $btn->src ? 'style="background-image:url(\'' . $btn->src . '\')"' : '';
                        $action = $btn->action ? 'onclick="' . $btn->action . '"' : '';
                        $href = 'href="' . ($btn->href && $btn->action ? Route::_($btn->href) : 'javascript:void(0);') . '"';

                        if ($btn->type == "BIG") {
                            if ($countMin)
                                echo "</ul>";
                            $countMin = 0;

                            echo '<ul id="' . $btn->id . '" class="big-btn-tools"' . $tooltip . '>';
                            echo '<li><a ' . $action . ' ' . $icon . ' class="icon-big-tool ' . $btn->icon . '" ' . $href . '></a></li>';
                            echo '<li><a  class="btn-big-tool"  href="javascript:void(0);">' . Text::_($btn->title) . '</a>';
                            if ($btn->menu) {
                                context_menu($btn->menu);
                            }
                            echo '</li>';
                            echo '</ul>';
                        } elseif ($btn->type == "MIN") {
                            if ($countMin >= 3) {
                                echo '</ul>';
                                $countMin = 0;
                            }
                            if ($countMin == 0)
                                echo '<ul class="min-btn-tools">';
                            $countMin++;
                            echo '<li id="' . $btn->id . '"' . $tooltip . '><a class="min-btn-tool" ' . $action . ' ' . $href . '><span class="min-icon-tools" ' . $icon . '></span>' . Text::_($btn->title) . '</a><a class="min-arr-tool" href="javascript:void(0);"></a>';

                            if ($btn->menu) {
                                context_menu($btn->menu);
                            }
                            echo '</li>';
                        }

                        if ($i == count($groups) && $countMin) {
                            echo '</ul>';
                        }
                    }
                    ?>
                </div>
                <?php
                $i++;
                if ($i < count($groups))
                    echo '<div class="tbs-siporator-single"></div>'
                    ?>
<?php endforeach; ?>
        </div>
        <div class="tbs-bar-right">
            <div class="tbs-siporator-duble"></div>
            <div class="tbs-bottom-group">
                <div class="tbs-bottom-editing-mode"><div><input type="checkbox" <?php echo EDITING_MODE ? 'checked="checked"' : '' ?>  name="editing_mode" value="1"/></div> <div><?php echo Text::_("LIB_USERINTERFACE_TOOLBAR_EDITING_MODE") ?> <?php echo EDITING_MODE ? '<span style="color:#1FB227">' . Text::_("LIB_USERINTERFACE_TOOLBAR_ENABLE") . '</span>' : '<span style="color:#494F59;">' . Text::_("LIB_USERINTERFACE_TOOLBAR_DISABLE") . '</span>' ?> </div></div>
                <a class="tbs-bottom-switch" href="javascript:void(0);"><span class="icon-tools-bar icon-arr-app"></span><?php echo Text::_("LIB_USERINTERFACE_FIELD_DOWNPANEL"); ?></a>
            </div>
        </div>
    </div><?php
    $mess = $this->document->getMessage(0);
    $text = isset($mess["text"]) ? $mess["text"] : '';
    if (isset($mess["type"])) {
        $class = $mess["type"] == "error" || $mess["type"] == "warning" ? ' class="error-messages"' : ' class="success-messages"';
    } else {
        $class = "";
    }
    ?>
    <div id="tool-bar-messages"<?php echo $class ?>><?php echo $text ?></div>
</div>