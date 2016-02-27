<?php 
$this->_doc->addStylesheet("/expansion/exp_".$this->_appname."/css/userstyle.css");	
$this->_doc->addStylesheet("/library/jquery/overlay/preloader/jquery-overlay-preloader.css");
$this->_doc->addScript("/library/jquery/overlay/preloader/jquery-overlay-preloader.js");
$this->_doc->addScript("/expansion/exp_".$this->_appname."/js/user.js");		
?>
<?php if(!$isUser):?>
<form  class="default-user-form" action="/user/?run=login" method="post">
<div class="wrap-container big-element-200 center-alignment">
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_LOGIN") ?></div>
  	<div class="user-cell-2"><input name="login" type="text"/></div>
  </div>
  <div class="user-row">
  	<div class="user-cell-2"><?php echo Text::_("EXP_USER_PASS_TITLE") ?></div>
  	<div class="user-cell-2"><input name="password" type="password"/></div>
  </div>
  <?php if($captcha):?>
  <div class="user-row">
  	<div class="user-cell-2"></div>
    <div class="user-cell-2"><img height="80" width="225px" src="<?php echo $captcha ?>"/><button onclick="return $.user.updateCaptcha(this)" class="btn btn-primary"><?php echo Text::_("EXP_USER_PASS_UPDATE") ?></button></div>
  </div>
   <div class="user-row">
   	<div class="user-cell-2"><?php echo Text::_("EXP_USER_PASS_CAPTCHA") ?></div>
  	<div class="user-cell-2"><input name="captcha" type="text"/></div>
  </div>
  <?php endif;?>
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button class="btn btn-success"><?php echo Text::_("EXP_USER_PASS_INPUT") ?></button></div>
  </div>
  <?php if($params->getVar("log_passbtn")):?>
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button onclick="return $.user.redirect('/user/?run=getRecover')" class="btn btn-primary"><?php echo Text::_("EXP_USER_PASS_EDITPASS") ?></button></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("log_regbtn")):?>
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button onclick="return $.user.redirect('/user/registration')" class="btn btn-primary"><?php echo Text::_("EXP_USER_PASS_REG") ?></button></div>
  </div>
  <?php endif;?>
</div>
</form>
<?php else:?>
<form class="default-user-form" action="/user/?run=logout" method="post">

<div class="wrap-container big-element-200 center-alignment">
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_AVATAR") ?></div>
  	<div class="user-cell-2"><img src="<?php echo $avatar; ?>" /></div>
  </div>
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_LOGIN") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $username; ?>" type="text"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_YOU") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $name; ?>" type="text"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_EMAIL") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $email ; ?>" type="text"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_LASTDAATE") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $lastvisitDate ?>" type="text"/></div>
  </div>
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button class="btn btn-warning"><?php echo Text::_("EXP_USER_LOGOUT") ?></button></div>
  </div>
  <?php if($params->getVar("log_editbtn")):?>
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button onclick="return $.user.redirect('/user/settings')" class="btn btn-primary"><?php echo Text::_("EXP_USER_EDIT") ?></button></div>
  </div>
  <?php endif;?>
</div>
</form>
<?php endif;?>
