<?php

use maze\helpers\Html;
?>
<script>
    jQuery(document).ready(function () {
        var installObj = new mazeInsatll();
        installObj.init()
    })
</script>
<?php $i = 0;
$c = 0;
foreach ($langs as $lang): ?>
        <?php if ($c == 0): ?>
        <div class="row">
    <?php endif; ?>
        <div class="col-md-3 col-sm-3">
            <a class="btn btn-default btn-lg btn-block<?= $lang['reduce'] == $curentModel->reduce ? " active" : "" ?>"  href="/install/install?step=0&lang=<?= $lang['reduce'] ?>"><img src="/library/image/flags/16/<?= $lang['img'] ?>"s/>  <?= $lang['title'] ?></a>
        </div>
    <?php $i++;
    $c++;
    if ($c == 4 || $i >= count($langs)): $c = 0; ?> 
        </div>
    <?php endif; ?>
<?php endforeach; ?>
<br/>


<?php foreach ($curentModel->attributes as $attribute => $val): ?>
    <?= Html::activeHiddenInput($curentModel, $attribute) ?>
<?php endforeach; ?>
  