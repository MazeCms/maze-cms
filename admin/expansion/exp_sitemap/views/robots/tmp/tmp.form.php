<?php

use maze\helpers\Html;
use ui\form\FormBuilder;
use maze\helpers\ArrayHelper;
use maze\helpers\Json;
?>
<div class="wrap-form">

    <?php
    $form = FormBuilder::begin([
                'ajaxSubmit' => true,
                'id' => 'sitemap-robots-form',
                'groupClass' => 'form-group',
                'dataFilter' => 'function(data){return data.errors}',
                'onAfterCheck' => 'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn(cms.getLang("LIB_USERINTERFACE_FIELD_SUBMITFORM_ERR"), error, "auto", 400)} return true;}'
    ]);
    ?>
    
    <div class="row">
       
        <div class="col-md-9"> 
            <?= $form->field($model, 'title'); ?>
            <?= $form->field($model, 'search'); ?>
        </div>
         <div class="col-md-3">
            <?= $form->field($model,'images')->element('ui\images\AddImage', ['settings' => ['max_img' => 1]]);?>
        </div>
    </div>
    
   
    
    <?php ui\form\FormBuilder::end(); ?>

</div>