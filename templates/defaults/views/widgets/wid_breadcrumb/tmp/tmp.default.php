<?php

use maze\helpers\Html;
?>
<nav id="breadcrumbs">
    <ul>
        <li>Вы здесь: </li>
        <li><a href="<?= RC::app()->request->getBaseUrl() ?>">Главная</a></li>
                <?php
                foreach ($path as $key => $items) {

                    if (isset($items['url']) && $key != count($path) - 1) {
                        echo '<li><a href="' . Route::_($items["url"]) . '">' . $items["label"] . '</a></li>';
                    } else {
                        echo '<li>' . $items["label"] . '</li>';
                    }
                }
                ?>
    </ul> 
</nav>

