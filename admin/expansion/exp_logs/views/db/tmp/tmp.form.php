<?php

use maze\base\JsExpression;
use maze\helpers\Html;
?>
<div class="wrap-form">
    <table class="table table-striped table-hover">
        <tbody>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_DATETIME_LABEL')?></strong></td>
                <td><?=$logs->datetime;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_IP_LABE')?></strong></td>
                <td><?=$logs->ip;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_USER_LABEL')?></strong></td>
                <td><?=$logs->user_id;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_SESSION_ID')?></strong></td>
                <td><?=$logs->session_id;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_CATEGORY_LABEL')?></strong></td>
                <td><?=$logs->category;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_TRACES_LABEL')?></strong></td>
                <td><?=$logs->traces;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_DB_TIME_LABEL')?></strong></td>
                <td><?=$logs->time;?></td>
            </tr>
            <tr>
                <td width="10%"><strong><?=Text::_('EXP_LOGS_DB_QUERY_LABEL')?></strong></td>
                <td><?=$logs->query;?></td>
            </tr> 
        </tbody>
    </table>
</div>