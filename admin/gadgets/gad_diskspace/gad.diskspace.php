<?php

use maze\helpers\Json;

ui\assets\AssetHighcharts::register(['publishOptions' => ['drilldown']]);
?>
<?php
$path = RC::getAlias($params->getVar('path'));
if(!is_dir($path)){
     echo '<div class="alert alert-danger" role="alert">'.Text::_('GAD_DISKSPACE_MESS_NODIR', ['path'=>$path]).'</div>';
    return;
}
$total = disk_total_space($path) / pow(1024, 2);
$free = disk_free_space($path) / pow(1024, 2);
$busy = $total - $free;
$basename = sprintf('%x', crc32($path));
$data = [
    ['name' => Text::_('GAD_DISKSPACE_FREE').' - ' . ceil($free) . 'MB', 'y' => ceil($free / $total * 100), 'color' => ($params->getVar('colorfree') ? $params->getVar('colorfree') :  '#3BE000'  )],
    ['name' => Text::_('GAD_DISKSPACE_BUSY').' - ' . ceil($busy) . 'MB', 'y' => ceil($busy / $total * 100), 'color' => ($params->getVar('colorbusy') ? $params->getVar('colorbusy') :  '#FF5744'  )]
];

?>
<div id="<?= $basename.'-'.$gadget->id_gad ?>"></div>
<?php if($params->getVar('showpath')):?>
<div><?=Text::_('GAD_DISKSPACE_PATH')?>: <?= $path ?> </div>
<?php endif;?>

<script>
    $(document).ready(function () {
        $('#<?= $basename.'-'.$gadget->id_gad ?>').highcharts({
            credits:{
                enabled:false
            },
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45
                }
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    innerSize: 100,
                    depth: 45                    
                }
            },
            series: [{
                name: '<?=Text::_('GAD_DISKSPACE_SIZE')?>',
                data: <?= Json::encode($data) ?>
            }]
        });
    })
</script>