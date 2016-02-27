<?php 
$this->_doc->addStylesheet("/expansion/exp_".$this->_appname."/css/userstyle.css");	
$this->_doc->addStylesheet("/library/jquery/overlay/preloader/jquery-overlay-preloader.css");
$this->_doc->addStylesheet("/library/jquery/ajaxupload/jquery.dialog.fileupload.css");
$this->_doc->addStylesheet("/library/jquery/dialog/maze/jquery-maze-dialog-1.0.css");	

$this->_doc->addScript("/library/jquery/overlay/preloader/jquery-overlay-preloader.js");
$this->_doc->addScript("/library/jquery/ajaxupload/jquery.fileupload.js");
$this->_doc->addScript("/library/jquery/ajaxupload/jquery.iframe-transport.js");
$this->_doc->addScript("/library/jquery/dialog/maze/jquery-maze-dialog-1.0.js");
$this->_doc->addScript("/library/jquery/ajaxupload/jquery.dialog.fileupload.js");
$this->_doc->addScript("/expansion/exp_".$this->_appname."/js/user.js");


$loadavatar = array();
$loadavatar["accessFile"] = $params->getVar("typeimage");
$loadavatar["limitFile"] = $params->getVar("sizeimage");
$this->_doc->setTextScritp("var optiomsLoad = ".json_encode($loadavatar).";");	
?>
<form class="default-user-form" action="/user/settings/?run=save" method="post">

<div class="wrap-container big-element-400 center-alignment">
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_AVATAR") ?></div>
  	<div class="user-cell-2"><img id="avatar-img" src="<?php echo $avatar ? $avatar : "/expansion/exp_user/img/user.png"?>" /></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"></div>
  	<div class="user-cell-2">
    <button onclick="return $.user.addAvatar('avatar-img')" class="btn btn-primary"><?php echo Text::_("EXP_USER_FORM_BTN_ADDAVATAR") ?></button>
    <button onclick="return $.user.delAvatar('avatar-img', '/expansion/exp_user/img/user.png')" class="btn btn-warning"><?php echo Text::_("EXP_USER_FORM_BTN_DELAVATAR") ?></button>
    </div>
  </div>
	<div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_LOGIN") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $username; ?>" type="text"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_YOU") ?></div>
  	<div class="user-cell-2"><input name="name" value="<?php echo $name; ?>" type="text"/></div>
  </div>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_MAIL_PASS_EMAIL") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $email ; ?>" type="text"/></div>
  </div>
  <?php if($params->getVar("set_time_out")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_TIMEOUT") ?></div>
  	<div class="user-cell-2"><input name="meta[time_out]" value="<?php echo $this->get("time_out") ; ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_id_lang")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_LANG") ?></div>
  	<div class="user-cell-2"><?php echo  Lang::_("id_lang", $id_lang, false, false, 425); ?></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_timezone")):?>
   <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_TIMEZONE") ?></div>
  	<div class="user-cell-2"><?php echo  TimeZone::_("meta[timezone]", $this->get("timezone") , false, false, 425); ?></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_registerDate")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_REGDATE") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $registerDate  ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_timeactiv")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_ACTIVEDATE") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $timeactiv  ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_lastvisitDate")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_LASTDAATE") ?></div>
  	<div class="user-cell-2"><input disabled="disabled" value="<?php echo $lastvisitDate ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <div class="user-row">
 		<div class="user-cell-1"><h3><?php echo Text::_("EXP_USER_FORM_TABS_CONTACTS_USER") ?></h3></div>
  </div>
  <?php if($params->getVar("set_first_name")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_NAME") ?></div>
  	<div class="user-cell-2"><input name="meta[first_name]" value="<?php echo $this->get("first_name"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_patronymic")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_PATRONYMIC") ?></div>
  	<div class="user-cell-2"><input name="meta[patronymic]" value="<?php echo $this->get("patronymic"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_surname")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_SURNAME") ?></div>
  	<div class="user-cell-2"><input name="meta[surname]" value="<?php echo $this->get("surname"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_country")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_COUNTRY") ?></div>
  	<div class="user-cell-2"><input name="meta[country]" value="<?php echo $this->get("country"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_city")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_CITY") ?></div>
  	<div class="user-cell-2"><input name="meta[city]" value="<?php echo $this->get("city"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_street")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_STREET") ?></div>
  	<div class="user-cell-2"><input name="meta[street]" value="<?php echo $this->get("street"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_home")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_HOME") ?></div>
  	<div class="user-cell-2"><input name="meta[home]" value="<?php echo $this->get("home"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_flat")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_FLAT") ?></div>
  	<div class="user-cell-2"><input name="meta[flat]" value="<?php echo $this->get("flat"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_phone")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_PHONE") ?></div>
  	<div class="user-cell-2"><input name="meta[phone]" value="<?php echo $this->get("phone"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_site")):?>
  <div class="user-row">
 		<div class="user-cell-2"><?php echo Text::_("EXP_USER_FORM_LABEL_META_SITE") ?></div>
  	<div class="user-cell-2"><input name="meta[site]" value="<?php echo $this->get("site"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_skype")):?>
  <div class="user-row">
 		<div class="user-cell-2">Skype</div>
  	<div class="user-cell-2"><input name="meta[skype]" value="<?php echo $this->get("skype"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
  <?php if($params->getVar("set_icq")):?>
  <div class="user-row">
 		<div class="user-cell-2">ICQ</div>
  	<div class="user-cell-2"><input name="meta[icq]" value="<?php echo $this->get("icq"); ?>" type="text"/></div>
  </div>
  <?php endif;?>
 
  <div class="user-row">
  	<div class="user-cell-2"></div>
  	<div class="user-cell-2"><button class="btn btn-success"><?php echo Text::_("LIB_USERINTERFACE_TOOLBAR_SAVE_BUTTON") ?></button></div>
  </div>

</div>
<input type="hidden" name="avatar" value="<?php echo $avatar ?>" />
<input type="hidden" name="id_user" value="<?php echo $id_user ?>" />
</form>