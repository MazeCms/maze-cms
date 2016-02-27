
<div class="tool-bar-admin">
    <div class="tbs-top-left">
        <div id="logo-cms"></div>
    </div>
    <div class="tbs-top-center">
        <a id="tabs-site-topbar" class="btn-top-bar tabs-bar" href="/"><?php echo Text::_("WID_NAVBAR_SITE") ?></a>
        <a id="tabs-admin-topbar" class="btn-top-bar tabs-bar active" href="/admin"><?php echo \Text::_("WID_NAVBAR_ADMIN") ?></a>
    </div>
    <div class="tbs-top-right">
        <?php if(RC::app()->access->roles("user", "EDIT_SELF_USER") && $user['id_user'] == RC::app()->access->getUid() ):?>
            <a class="btn-top-bar active" href="/admin/user/?run=edit&id_user=<?php echo $user['id_user'] ?>"><span class="icon-top-bar bar-icon-user"></span> <?php echo $user['username'] ?></a>
        <?php else:?>
            <a class="btn-top-bar active" href="#"><span class="icon-top-bar bar-icon-user"></span> <?php echo $user['username'] ?></a>
        <?php endif;?>        
        <a class="btn-top-bar" href="/user/?run=logout"><?php echo Text::_("WID_NAVBAR_OUT") ?></a>
    </div>
</div>
