<?php 
$this->_doc->addStylesheet("/expansion/exp_".$this->_appname."/css/userstyle.css");	
$this->_doc->addStylesheet("/library/jquery/overlay/preloader/jquery-overlay-preloader.css");
$this->_doc->addScript("/library/jquery/overlay/preloader/jquery-overlay-preloader.js");
$this->_doc->addScript("/expansion/exp_".$this->_appname."/js/user.js");		
?>
<form  class="default-user-form" action="/user/registration?run=record" method="post">
<div class="wrap-container big-element-200 center-alignment">
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_LOGIN") ?></div>
  	<div class="user-cell-2"><input name="login" type="text"/></div>
  </div>
  <div class="user-row">
  	<div class="user-cell-2">E-mail</div>
  	<div class="user-cell-2"><input name="email" type="text"/></div>
  </div>
  <div class="user-row">
  	<div class="user-cell-2"><?php echo Text::_("EXP_USER_PASS_TITLE") ?></div>
  	<div class="user-cell-2"><input name="password" type="password"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_PASS_VERIFIED") ?></div>
  	<div class="user-cell-2"><input name="verified" type="password"/></div>
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
  	<div class="user-cell-2"><button class="btn btn-success"><?php echo Text::_("EXP_USER_SEND") ?></button></div>
  </div>
</div>
</form>
