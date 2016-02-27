<?php

    $controller = RC::app()->getController();
    $controller->loadController();
    echo $controller->run();
?>