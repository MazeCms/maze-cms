<div class="widget">
    <?php if (isset($title_show) && $title_show): ?>
    <h3 clas="headline" data-winget-id="<?php echo $id_wid ?>"><?php echo $title ?></h3>
    <span class="line"></span>    
    <?php endif; ?>
    <div class="clearfix"></div>
    <?php echo $body ?>
</div>
