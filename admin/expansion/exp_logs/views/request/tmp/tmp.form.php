<?php

use maze\base\JsExpression;
use maze\helpers\Html;
use maze\helpers\VarDumper;
use ui\tabs\JqTabs;

?>
<?php $tabs = JqTabs::begin(['options' => ['id' => 'admin-logs-request']]); ?> 
<?php $tabs->beginTab(Text::_("EXP_LOGS_REQUEST_TABS_GENERAL")); ?> 

<table class="table table-striped table-hover">
    <tbody>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_DATETIME_LABEL') ?></strong></td>
            <td><?= $logs->datetime; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_IP_LABE') ?></strong></td>
            <td><?= $logs->ip; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_USER_LABEL') ?></strong></td>
            <td><?= $logs->user_id; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_SESSION_ID') ?></strong></td>
            <td><?= $logs->session_id; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_CATEGORY_LABEL') ?></strong></td>
            <td><?= $logs->category; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_REQUEST_STATUSTEXT_LABEL') ?></strong></td>
            <td><?= $logs->statusText; ?></td>
        </tr>
        <tr class="<?= ($logs->statusCode >= 400  && $logs->statusCode <= 500) ? "danger" : ($logs->statusCode == 200 ? "success" : "warning"); ?>">
            <td width="10%"><strong><?= Text::_('EXP_LOGS_REQUEST_STATUSCODE_LABEL') ?></strong></td>
            <td><?= $logs->statusCode; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_REQUEST_ROUTE_LABEL') ?></strong></td>
            <td><?= $logs->route; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_REQUEST_ACTION_LABEL') ?></strong></td>
            <td><?= $logs->action; ?></td>
        </tr>
        <tr>
            <td width="10%"><strong><?= Text::_('EXP_LOGS_REQUEST_CONTROLLER_LABEL') ?></strong></td>
            <td><?= $logs->controller; ?></td>
        </tr>
    </tbody>
</table>
<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_LOGS_REQUEST_TABS_HEADERS")); ?>
<table class="table table-striped table-hover">
    <tbody>
        <tr>
            <td colspan="2"><h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_REQUEST")?></h4></td>
        </tr>
        <?php foreach ($logs->requestHeaders as $name => $value): if (empty($value)) continue; ?>
            <tr>
                <td width="10%"><strong><?= $name ?></strong></td>
                <td><?= VarDumper::dumpAsString(end($value)); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2"><h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_RESPONSE")?></h4></td>
        </tr>
        <?php foreach ($logs->responseHeaders as $name => $value): if (empty($value)) continue; ?>
            <tr>
                <td width="10%"><strong><?= $name ?></strong></td>
                <td><?= VarDumper::dumpAsString($value); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>        
<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_LOGS_REQUEST_HEADERS_GLOBAL_PARAMS")); ?>
<h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_GLOBAL_GET")?></h4>
<table class="table table-striped table-hover">
    <tbody>
        <?php foreach ($logs->get as $name => $value): ?>
        <tr>
            <td width="10%"><strong><?= $name ?></strong></td>
            <td><?= VarDumper::dumpAsString($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table> 
<h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_GLOBAL_POST")?></h4>
<table class="table table-striped table-hover">
    <tbody>
        <?php foreach ($logs->post as $name => $value): ?>
        <tr>
            <td width="10%"><strong><?= $name ?></strong></td>
            <td><?= VarDumper::dumpAsString($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table> 
<h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_GLOBAL_COOKIE")?></h4>
<table class="table table-striped table-hover">
    <tbody>
        <?php foreach ($logs->cookie as $name => $value): ?>
        <tr>
            <td width="10%"><strong><?= $name ?></strong></td>
            <td><?= VarDumper::dumpAsString($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<h4><?=Text::_("EXP_LOGS_REQUEST_HEADERS_GLOBAL_SESSION")?></h4>
<table class="table table-striped table-hover">
    <tbody>
        <?php foreach ($logs->session as $name => $value): ?>
        <tr>
            <td width="10%"><strong><?= $name ?></strong></td>
            <td><?= VarDumper::dumpAsString($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $tabs->endTab(); ?>
<?php $tabs->beginTab(Text::_("EXP_LOGS_REQUEST_TABS_SERVER")); ?>
<table class="table table-striped table-hover">
    <tbody>
        <?php foreach ($logs->server as $name => $value): ?>
        <tr>
            <td width="10%"><strong><?= $name ?></strong></td>
            <td><?= VarDumper::dumpAsString($value); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $tabs->endTab(); ?>
<?php JqTabs::end(); ?>