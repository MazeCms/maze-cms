<script>
    jQuery(document).ready(function () {
        var installObj = new mazeInsatll();
        installObj.init()
    })
</script>
<table class="table-check">
    <thead>
        <tr>
            <th><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_NAME")?></th>
            <th><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_REQUIRED")?></th>
            <th><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_PRESVALUE")?></th>
        </tr>
    </thead>
    <tbody>        
        <tr class="<?= $curentModel->validate(["servern", "serverv"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("serverv")?></td>
            <td class="text-center">Apache 2.2</td>
            <td class="text-center"><?= $curentModel->servern ?> <?= $curentModel->serverv ?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["phpv"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("phpv")?></td>
            <td class="text-center">5.6</td>
            <td class="text-center"><?=$curentModel->phpv?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="h4"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_INSTALL_PHP")?>:</div>
            </td>
        </tr>
        <tr class="<?= $curentModel->validate(["safe_mode"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("safe_mode")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->safe_mode ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["pdo"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("pdo")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->pdo ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["pdo_mysql"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("pdo_mysql")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->pdo_mysql ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["reflection"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("reflection")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->reflection ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["curl"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("curl")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->curl ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["zipArchive"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("zipArchive")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->zipArchive ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["simpleXml"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("simpleXml")?></td>
            <td class="text-center">Включено</td>
            <td class="text-center"><?= $curentModel->simpleXml ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["mbstring"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("mbstring")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->mbstring ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["gd"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("gd")?></td>
            <td class="text-center">Включено</td>
            <td class="text-center"><?= $curentModel->gd ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr class="<?= $curentModel->validate(["json"]) ? "success" : "danger"?>">
            <td><?=$curentModel->getAttributeLabel("json")?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON")?></td>
            <td class="text-center"><?= $curentModel->json ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ON") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_OFF")?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="h4"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ACC_DIR")?>:</div>
            </td>
        </tr>
        <?php foreach($curentModel->path as $path):?>
        <tr class="<?= $curentModel->getPathRW($path) ? "success" : "danger"?>">
            <td><?=RC::getAlias($path)?></td>
            <td class="text-center"><?=Text::_("LIB_FRAMEWORK_INSTALL_PARAM_ACC_VAL")?></td>
            <td class="text-center"><?= $curentModel->getPathRW($path) ? Text::_("LIB_FRAMEWORK_INSTALL_PARAM_AVAILABLE") : Text::_("LIB_FRAMEWORK_INSTALL_PARAM_UNAVAILABLE")?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

