<?php 
use maze\table\Sessions;
use maze\helpers\Html;
use maze\helpers\DataTime;

$sesQuery = Sessions::find()->joinWith('user')->from(['s' =>Sessions::tableName()]);
$count =   $sesQuery->count();

$sessions =  $sesQuery->limit($params->getVar('size'))->all();

?>
<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="2"><?=Text::_("GAD_ONLINEUSER_USER_LABEL")?></th>
            <?php if($params->getVar('showip')):?>
                <th>IP</th>
            <?php endif;?>
            <?php if($params->getVar('showos')):?>
                <th><?=Text::_("GAD_ONLINEUSER_OS_LABEL")?></th>
            <?php endif;?>
            <?php if($params->getVar('showbrowser')):?>    
                <th><?=Text::_("GAD_ONLINEUSER_BROWSER_LABEL")?></th>
            <?php endif;?>
            <?php if($params->getVar('showtime')):?>    
                <th><?=Text::_("GAD_ONLINEUSER_TIME_LABEL")?></th>
            <?php endif;?>    
        </tr>
    </thead>
<?php foreach($sessions as $ses):?>
    <tr>
        <td width="50px">
           <?php $avatar = $ses->user && $ses->user->avatar ? $ses->user->avatar : '/library/image/custom/user.png'; ?> 
            <?php echo Html::imgThumb('@root' . $avatar, 50, 50, ['class'=>'media-object'])?> 
        </td>
        <td>
            <h5><?php echo $ses->user ? $ses->user->name : Text::_('EXP_USER_SESSIONS_TABLE_ANONIM');?></h5>
        </td>
        <?php if($params->getVar('showip')):?>
        <td>
           <?=$ses->ip?>
        </td>
        <?php endif;?>
        <?php if($params->getVar('showos')):?>
        <td>
           <?=RC::app()->request->getOS($ses->agent);?>
        </td>
        <?php endif;?>
        <?php if($params->getVar('showbrowser')):?>
        <td>
            <?=RC::app()->request->gerBrowser($ses->agent);?>
        </td>
        <?php endif;?>
        <?php if($params->getVar('showtime')):?>
        <td><?= DataTime::diffMinutes($ses->time_start, $ses->time_last)?> мин</td>
        <?php endif;?>
    </tr>
<?php endforeach;?>
</table>
<?php if($params->getVar('showalluser')):?>
<ul class="nav nav-pills" role="tablist">
  <li role="presentation"><?=Text::_("GAD_ONLINEUSER_ALLUSER_LABEL")?> <span class="badge"><?=$count?></span></li>
</ul>
<?php endif;?>