<?php
    use maze\helpers\Html;
    
?>
<?php  if (is_array($data)): ?>
    <?php  foreach ($data as $d): ?>
        <?php
            $content = $d->label_file;
            if($param->showSize){
               $content .= ' '.$d->sizeToBytes($param->formatSize);
            }
            echo Html::button($content, ['data-href'=>RC::getAlias('@web/'.$d->path_file), 'class'=>$param->cssClass, 'onclick'=>$param->onclick]);
        ?>
    <?php endforeach; ?>
<?php else: ?>
         <?php
            $content = $data->label_file;
            if($param->showSize){
               $content .= ' '. $data->sizeToBytes($param->formatSize);
            }
            $content .= '<span class="btn-icon-download"></span>';
            echo Html::button($content, ['data-href'=>RC::getAlias('@web/'.$data->path_file), 'class'=>$param->cssClass, 'onclick'=>$param->onclick]);
        ?>
<?php endif; ?>
