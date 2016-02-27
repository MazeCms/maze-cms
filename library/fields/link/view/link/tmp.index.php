<?php

use maze\helpers\Html;

?>
<?php if (!empty($data)): ?>
    <?php if (is_array($data)): ?>
        <?php foreach ($data as $d): ?>
            <?php

            $options = ['class' => $param->cssClass];
            if ($param->onclick) {
                $url = '#';
                $options['data-href'] = $d->link_url;
                $options['onclick'] = $param->handler;
            } else {
                $url = $d->link_url;
            }
            echo Html::a($d->link_label, $url, $options);
            ?>
        <?php endforeach; ?>
    <?php else: ?>
        <?php

        $options = ['class' => $param->cssClass];
        if ($param->onclick) {
            $url = '#';
            $options['data-href'] = $data->link_url;
            $options['onclick'] = $param->handler;
        } else {
            $url = $data->link_url;
        }
        echo Html::a($data->link_label, $url, $options);
        ?>
    <?php endif; ?>
<?php endif; ?>