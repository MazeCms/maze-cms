<?php
use maze\helpers\Html;
?>
<div><?= Text::_('EXP_USER_MAIL_HELLO') ?> - <?= RC::app()->config->get('site_name') ?></div>
<div><?= $user->name ?></div>
<div><?= Text::_('EXP_USER_MAIL_TEXT') ?></div>
<div><?= Text::_('EXP_USER_MAIL_MESS') ?></div>
<a href="<?=(new URI(['@web/user/recover', ['run'=>'editPass', 'code'=>$user->keyactiv]]))->toString()?>" target="_blank">Поехали</a>