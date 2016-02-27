<h1><?=Text::_('EXP_USER_REC_PASSOK')?></h1>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
    <tr>
        <td><?= Text::_('EXP_USER_LOGIN') ?></td>
        <td><?=$user->username?></td>
    </tr>
    <tr>
        <td>e-mail</td>
        <td><?=$user->email?></td>
    </tr>
    <tr>
        <td><?= Text::_('EXP_USER_PASS') ?></td>
        <td><?=$password?></td>
    </tr>	
</table>