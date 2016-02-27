<?php if ($data): ?>
    <?php $model = $param->getModelContent($data); ?>
    <?php if ($model): ?>
        <div class="content-type-<?= end($model['modelByid'])->contents->bundle ?>">
            <table class="<?=$param->cssclass?>">
                <thead>
                    <tr>
                        <?php foreach (end($model['views'])['v'] as $name => $view): ?>
                            <th><?= $view->renderLabel; ?></th>
                        <?php endforeach; ?>
                    </tr> 
                </thead>
                <tbody>
                    <?php foreach ($model['views'] as  $filedView): ?>
                        <tr>
                            <?php $model['modelByid'][$filedView['id']]->toolbar->getStart(); ?>  
                            <?php foreach ($filedView['v'] as $name => $view): ?>
                               <td> <?= $view->beginWrap; ?>
                                <?= $view->renderField; ?>
                                <?= $view->endWrap; ?>
                               </td>
                            <?php endforeach; ?>
                            <?= $model['modelByid'][$filedView['id']]->toolbar->run(); ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
