<?php
ui\assets\AssetTree::register();
ui\assets\AssetCodeMirror::register();
ui\assets\AssetFancybox::register();

$this->addStylesheet(RC::app()->getExpUrl("/css/style.css"));
$this->addScript(RC::app()->getExpUrl("/js/template.js"));
?>
<script>
 appTemplate.src = '<?php echo $src;?>';
</script>
<div id="wrapp-tabs">
    <div id="code-editor" class="admin-tabs-default">
        <ul>
            <li><a href="#tabs-1">Файлы</a></li>
        </ul>
        <div id="tabs-1">
            <div id="tmp-tree"></div>
        </div>
    </div>
</div>
