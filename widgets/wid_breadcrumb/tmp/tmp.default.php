<?php

use maze\helpers\Html;

?>

<?php if(!empty($path)):?>
<ol class="breadcrumb">
<?php 
  foreach($path as $key=>$items)
  {
        
        if(isset($items['url'])){
            echo '<li><a href="'.Route::_($items["url"]).'">'.$items["label"].'</a></li>';
        }else{
            echo '<li class="active">'.$items["label"].'</li>';
        }
       
	  
  }
?>
</ol>
<?php endif;?>