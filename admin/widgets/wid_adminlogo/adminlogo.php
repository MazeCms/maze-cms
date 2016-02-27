<?php
defined('_CHECK_') or die("Access denied");


ui\assets\AssetJquery::register();
ui\assets\AssetLoad::register();
wid\wid_adminlogo\AssetWidget::register();

$modelLogin = new wid\wid_adminlogo\Login();
$modelRecover = new wid\wid_adminlogo\Recover();
$isRecover = RC::app()->getComponent('user')->config->getVar('recBloc');
?>

<div class="notice notice-danger">
    <a href="" class="close">close</a>
    <p class="warn"></p>
</div>

<div class="container container-from-login">

    <div class="form-bg">
        <?php
        $login = ui\form\FormBuilder::begin([
                    'ajaxSubmit' => true,
                    'action' => ['/user', ['run' => 'login']],
                    'id' => 'user-login',
                    'options' => ['class' => 'form'],
                    'groupClass' => 'group-form',
                    'dataFilter' => 'function(data){return data.errors}'
                ])
        ?>
        <h2><?= Text::_('WID_ADMINLOGO_HEADER_LOGO') ?></h2>    
        <?= $login->field($modelLogin, 'login', ['template' => '{input}'])->textInput(['class' => 'form-element', 'placeholder' => $modelLogin->getAttributeLabel('login')]); ?>
        <?= $login->field($modelLogin, 'password', ['template' => '{input}'])->passwordInput(['class' => 'form-element', 'placeholder' => $modelLogin->getAttributeLabel('password')]); ?>
        <?= $login->field($modelLogin, 'remember', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>
        <button class="button full-width" type="submit"><?= Text::_('WID_ADMINLOGO_SUBMIT_LOGO') ?></button>
        <?php ui\form\FormBuilder::end(); ?>
        <?php if($isRecover):?>
        <?php
        $recover = ui\form\FormBuilder::begin([
                    'ajaxSubmit' => true,
                    'action' => ['/user/recover'],
                    'id' => 'user-recover',
                    'options' => ['class' => 'form'],
                    'groupClass' => 'group-form',
                    'dataFilter' => 'function(data){return data.errors}'
                ])
        ?>
        <h2><?= Text::_('WID_ADMINLOGO_HEADER_PASS') ?></h2>    
        <?= $login->field($modelRecover, 'login', ['template' => '{input}'])->textInput(['class' => 'form-element', 'placeholder' => Text::_('WID_ADMINLOGO_RECOVERY_PASS')]); ?>
        <button class="button full-width" type="submit"><?= Text::_('WID_ADMINLOGO_SUBMIT_PASS') ?></button>
        <?php ui\form\FormBuilder::end(); ?>
        <?php endif;?>
    </div>
    <?php if($isRecover):?>
    <p class="forgot"><span data-text="Как авторизоваться?"><?= Text::_('WID_ADMINLOGO_FORM_PASS_NAME') ?></span> 
    <a data-text="<?= Text::_('WID_ADMINLOGO_SUBMIT_LOGO') ?>" class="toggele-form" href="#"><?= Text::_('WID_ADMINLOGO_SUBMIT_RECOVER') ?></a></p>
    <?php endif;?>

</div>