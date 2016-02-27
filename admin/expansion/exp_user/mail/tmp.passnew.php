<div><?= Text::_('EXP_USER_MAIL_HELLO') ?> - <?= RC::app()->config->get('site_name') ?></div>
<?php if ($form->scenario == 'create'): ?>
    <div><?= Text::_('EXP_USER_MAIL_REGSITE') ?></div>
<?php else: ?>
    <div><?= Text::_('EXP_USER_MAIL_PASSEDIT') ?></div>
<?php endif; ?>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
    <tr>
        <td><?= Text::_('EXP_USER_TABLE_HEAD_LOGIN') ?></td>
        <td><?=$form->username?></td>
    </tr>
    <tr>
        <td>e-mail</td>
        <td><?=$form->email?></td>
    </tr>
    <tr>
        <td><?= Text::_('EXP_USER_FORM_LABEL_PASS') ?></td>
        <td><?=$form->new_password?></td>
    </tr>	
</table>