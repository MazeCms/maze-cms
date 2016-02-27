<?php
    use wid\wid_toolbar\helpers\Menu;
    use maze\helpers\Html;
    $groups = RC::app()->toolbar->group;
    
?>
<?php if($groups):?>
<div id="tool-bar-admin">
    <div class="tbs-bottom-tools">
        <div class="tbs-siporator-duble"></div>
        <div class="tbs-panel-buttons">
            <?php
            
            $i = 0;
            
            foreach ($groups as $group):
                $realGroup = 0;
                ?>
                <div class="tbs-bottom-group">      	
                    <?php
                    $countMin = 0;
                    foreach ($group as $btn) {
                        if(!$btn->visible) continue;
                        $tooltip = $btn->hint ? ' title="<div class=\'btn-tooltip-bar\'>' . Text::_($btn->hint["TITLE"]) . '</div>' . Text::_($btn->hint["TEXT"]) . '"' : '';
                        $icon = $btn->src ? 'style="background-image:url(\'' . $btn->src . '\')"' : '';
                        $action = $btn->action ? Html::renderTagAttributes(['onclick'=>$btn->action]) : '';
                        $href = 'href="' . ($btn->href && $btn->action ? Route::_($btn->href) : 'javascript:void(0);') . '"';

                        if ($btn->type == "BIG") {
                            if ($countMin)
                                echo "</ul>";
                            $countMin = 0;

                            echo '<ul id="' . $btn->id . '" class="big-btn-tools"' . $tooltip . '>';
                            echo '<li><a ' . $action . ' ' . $icon . ' class="icon-big-tool ' . $btn->icon . '" ' . $href . '></a></li>';
                            echo '<li><a  class="btn-big-tool"  href="javascript:void(0);">' . Text::_($btn->title) . '</a>';
                            if ($btn->menu) {
                               echo Menu::renderMenu($btn->menu);
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
                                echo Menu::renderMenu($btn->menu);
                            }
                            echo '</li>';
                        }

                        if ($i == count($groups) && $countMin) {
                            echo '</ul>';
                        }
                        
                        $realGroup++;
                    }
                    ?>
                </div>
                <?php $i++;
                if ($i < count($groups) && $realGroup > 0)
                    echo '<div class="tbs-siporator-single"></div>'
                    ?>
<?php endforeach; ?>

        </div>
        <div class="tbs-bar-right">
            <div class="tbs-siporator-duble"></div>
            <div class="tbs-bottom-group">
                <a href="javascript:void(0);" id="fixed-top-bar"><span class="icon-tool-bar bar-icon-clip"></span></a>
            </div>
        </div>
    </div>
    <?php
            
            $text = isset($mess["text"]) ? $mess["text"] : '';
            if (isset($mess["type"])) {
                $class = $mess["type"] == "error" || $mess["type"] == "warning" ? ' class="error-messages"' : ' class="success-messages"';
            } else {
                $class = "";
            }
            ?>
    <div id="tool-bar-messages" <?php echo $class ?>><?php echo $text ?></div>
</div>
<?php endif;?>