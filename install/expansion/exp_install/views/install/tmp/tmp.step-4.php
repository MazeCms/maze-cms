<?php

use maze\helpers\Html;

$profiles = $stepModel->getProfiles();
?>
<script>
    jQuery(document).ready(function () {
        var installObj = new mazeInsatll();
        installObj.init()
    })
</script>
<?php $c = 0;
$i = 0;
foreach ($profiles as $prof): ?>
    
    <?php if ($c == 0) {
        echo '<div class="row">';
    } ?>
    <div class="col-sm-4 col-md-4">
        <div class="thumbnail">
           <?=Html::imgThumb($prof->get('img'), 242, 200)?>
            <div class="caption">
                <h3><?= $prof->get('title') ?></h3>
                <p><?= $prof->get('description') ?><p>
                <div class="text-center">
                    <?php echo Html::activeRadio($curentModel, 'name', ['value'=>$prof->get('name')]);?> 
                </div>
            </div>
        </div>
    </div>

    <?php $i++;
    $c++;
    if ($c >= 3 || $i >= count($profiles)) {
        echo "</div>";
    } ?>
<?php endforeach; ?>

