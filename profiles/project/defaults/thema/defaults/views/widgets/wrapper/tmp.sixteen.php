<?php if (isset($title_show) && $title_show): ?>
<div class="sixteen columns" data-winget-id="<?php echo $id_wid ?>">    
    <h3 class="headline"><?php echo $title ?></h3>
    <span class="line" style="margin-bottom:0;"></span>
</div>
<?php endif; ?>
<?php echo $body ?>