<?php
use maze\helpers\Html;
use ui\form\FormBuilder;
use ui\tabs\JqTabs;
use ui\tabs\JqAccordion;
?>
<script>
    jQuery(document).ready(function ($) {
        var $component = $('#item-component'),
            $controller = $('#item-controller'),
            $view = $('#item-view'),
            $layout = $('#item-layout'),
            $typeLink = $('#item-typelink'),
            $menuType = $('.menu-type-expansion');
        
        function getId(elem) {
            return elem.attr('href').replace(/#/, '');
        }
        
        $typeLink.change(function(){
            cms.redirect([{typeLink:$(this).val()}])
        }); 
        
        
        $('.menu-type-expansion a').click(function () {
            var $self = $(this)                
            var $parent = $self.closest('.menu-type-expansion');
            if ($self.closest('.groupmenu-app').is('.groupmenu-app'))
            {
                $parent.find('.groupmenu-view > ul, .groupmenu-layout > ul').hide()
                $parent.find('.active').removeClass('active');
                $self.closest('li').addClass('active');
                $parent.find('.groupmenu-view > ul[data-app=' + getId($self) + ']').show();
                $component.val(getId($self));
                $view.val(null);
                $controller.val(null);
                $layout.val(null);
            }
            if ($self.closest('.groupmenu-view').is('.groupmenu-view'))
            {
                $parent.find('.groupmenu-layout > ul').hide()
                $parent.find('.groupmenu-view > ul, .groupmenu-layout > ul').find('.active').removeClass('active');
                $self.closest('li').addClass('active');
                var $target = $parent.find('.groupmenu-layout ul[data-app=' + getId($parent.find('.groupmenu-app .active a')) + ']')
                $target.each(function () {
                    if ($(this).is('[data-view=' + getId($self) + ']'))
                        $(this).show();
                })
                
                $view.val(getId($self));
                $controller.val(null);
                $layout.val(null);
            }
            if ($self.closest('.groupmenu-layout').is('.groupmenu-layout'))
            {
                $self.closest('.groupmenu-layout').find('.active').removeClass('active');
                $self.closest('li').addClass('active');
                $controller.val($self.attr('data-controller'));
                $layout.val(getId($self));
                cms.redirect([{
                     component: $component.val(),
                     controller: $controller.val(),
                     view:$view.val(),
                     layout:$layout.val()
                }]);
            }
            return false;
        }).tooltip({
            tooltipClass: 'dark-tooltip-bar',
            show: {
                effect: "fade",
                delay: 200
            },
            hide: {
                effect: "fade",
                delay: 200
            },
            position: {
                my: 'left center',
                at: 'right center',
            }
        });
        
        $('.menu-type-expansion').find('.groupmenu-view > ul, .groupmenu-layout > ul').hide()
        $('.menu-type-expansion').find('.active').removeClass('active');
        
        if($component.val())
        {
           var $targetApp = $('.menu-type-expansion .groupmenu-app').find('a[href=#'+$component.val()+']');
           if($targetApp.is('a'))
           {
               $targetApp.closest('li').addClass('active');
               $menuType.find('.groupmenu-view > ul[data-app=' + getId($targetApp) + ']').show();
           }
        }
        if($view.val())
        {
            var $targetView = $('.menu-type-expansion .groupmenu-view').find('[data-app='+$component.val()+'] a[href=#'+$view.val()+']');
            
            if($targetView.is('a'))
            {
                $targetView.closest('li').addClass('active');
                $menuType.find('.groupmenu-layout > ul[data-app=' + $component.val() + ']').each(function(){
                   if ($(this).is('[data-view=' + getId($targetView) + ']'))
                   {
                       $(this).show();                     
                       return false;
                   }                        
                })
                
            }
        }
        
        if($layout.val())
        {
            $menuType.find('.groupmenu-layout > ul[data-app=' + $component.val() + ']').each(function(){
               if ($(this).is('[data-view=' +$view.val()+ ']'))
               {
                   if($(this).find('a[href=#'+$layout.val()+']').is('a'))
                   {
                       $(this).find('a[href=#'+$layout.val()+']').each(function(){
                          if($(this).attr('data-controller') == $controller.val()){
                            $(this).closest('li').addClass('active');
                          } 
                       })
                       
                       return false;
                   }                   
               }                        
            })
        }
        
        $('#item-id_group').change(function(){
            var $self = $(this)
            if($self.val() == ''){
                $('#item-parent').find('option').filter(function(){return $(this).val() !== ''}).remove()
                $('#item-parent').mazeSelectTree('update');
                return;
            }
            $('#item-parent').mazeSelectTree('load',{id_group:$self.val(), id_menu:$('#item-id_menu').val()});
        })

    })
</script>

<div class="wrap-form">    
<?php

$form = FormBuilder::begin([
            'ajaxSubmit' => true,
            'id'=>'menu-form-groupmenu',
            'groupClass' => 'form-group has-feedback',
            'dataFilter' => 'function(data){return data.errors}',
            'onAfterCheck'=>'function (error, e) {if (error.length > 0 && e.type == "submit"){cms.alertBtn("Ошибка отправки формы", error, "auto", 400)} return true;}',
            'onErrorElem' => 'function (elem) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'elem.after("<span class=\"glyphicon glyphicon-remove form-control-feedback\"></span>");}}',
            'onSuccessElem' => 'function (elem, skipOnEmpty) {if (elem.is("input[type=text]")){elem.siblings(".form-control-feedback").remove();' .
            'if (!skipOnEmpty){elem.after("<span class=\"glyphicon glyphicon-ok form-control-feedback\"></span>")} }}',
            'onReset' => 'function(){this.find(".form-control-feedback").remove();}'
        ]);
echo $form->field($modelForm, 'name');

echo $form->field($modelForm, 'alias')->element('ui\text\InputAlias', ['options' => ['class' => 'form-control', 'placeholder'=>$modelForm->getAttributeLabel('alias')]]); 


echo $form->field($modelForm, 'typeLink')->element('ui\select\Chosen', ['items' => $modelMenu->listTypeLink(),
            'options' => ['class' => 'form-control']]);
?>
 <?php if($modelForm->scenario == 'alias' || $modelForm->scenario == 'url'):?>
    <?= $form->field($modelForm, 'paramLink');?>
 <?php endif;?>   
 <?php if($modelForm->scenario == 'expansion'):?>   
 <div class="menu-type-expansion">
    <div class="col-xs-4 groupmenu-app">
        <ul>
            <?php foreach ($menuSite->app as $app => $conf): if (empty($menuSite->views[$app])) continue; ?>
                <li>
                    <a title="<?= $conf->get('description'); ?>" href="#<?= $app; ?>"><?= $conf->get('name'); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-xs-4 groupmenu-view">
        <?php foreach ($menuSite->views as $app => $view): ?>
            <ul data-app="<?= $app; ?>">
            <?php foreach ($view as $viewObj): ?>
                <li>
                    <a title="<?= $viewObj->description; ?>" href="#<?= $viewObj->view; ?>"><?= $viewObj->title; ?></a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>
    <div class="col-xs-4 groupmenu-layout">
        <?php foreach ($menuSite->views as $app => $view): ?>
                <?php foreach ($view as $viewObj): ?>
                <ul data-app="<?= $app; ?>" data-view="<?= $viewObj->view; ?>">
                    <?php foreach ($viewObj->layouts as $layout): ?>                            
                    <li><a data-controller="<?= $layout->controller; ?>" title="<?= $layout->description; ?>" href="#<?= $layout->layout; ?>"><?= $layout->title; ?></a></li>                            
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>            
        <?php endforeach; ?>
    </div>
</div>
    <?php if($urlparams):?>
        <?php foreach ($urlparams->element as $element): ?>
        <div class="form-group">
            <?= ui\help\Tooltip::element(['content'=>Text::_($element['title']), 'help'=>(isset($element['description']) ? $element['description'] : null)]);?>
            <?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'url_param')) ?>
        </div>
        <?php endforeach;?>
    <?php endif;?>
<?php endif;?>
<?php $tabs = JqTabs::begin(['options'=>['id'=>'admin-tabs-groupmenu']]);?> 
 <?php $tabs->beginTab(Text::_("EXP_MENU_ADD_ITEM_FORM_TABSONE"));?>     
   <div class="col-md-6">
     <?= $form->field($modelForm,'id_lang')->element('ui\select\Chosen', ['items' => $modelMenu->listLang(),
        'options' => ['class' => 'form-control',  'prompt'=>'-- '.$modelForm->getAttributeLabel('id_lang').' --']]);
    ?>
    <?= $form->field($modelForm,'id_tmp')->element('ui\select\Chosen', ['items' => $modelMenu->listTmp(),
        'options' => ['class' => 'form-control', 'prompt'=>'-- '.$modelForm->getAttributeLabel('id_tmp').' --']]);
    ?>
    <?= $form->field($modelForm,'id_role')->element('ui\role\Roles', ['options' => ['class' => 'form-control', 
        'multiple' => 'multiple'], 'settings'=>['placeholder_text'=>'-- '.$modelForm->getAttributeLabel('id_role').' --']]);
    ?>
     <?= $form->field($modelForm,'image')->element('ui\images\AddImage', ['settings' => ['max_img' => 1]]);
    ?> 
    <?= $form->field($modelForm, 'enabled', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?>  
    <?= $form->field($modelForm, 'home', ['template' => '{input}'])->element('ui\checkbox\SwitchBtn'); ?> 
   
    <?= $form->field($modelForm, 'time_active')->element('ui\date\Datetimepicker', 
        [ 'options' => ['class' => 'form-control'] ]);?>
    <?= $form->field($modelForm, 'time_inactive')->element('ui\date\Datetimepicker', 
        [ 'options' => ['class' => 'form-control'] ]);?>   
   </div>
   <div class="col-md-6">
       <?= $form->field($modelForm,'id_group')->element('ui\select\Chosen', ['items' => $modelMenu->listMenu(),
        'options' => ['class' => 'form-control',  'prompt'=>'-- '.$modelForm->getAttributeLabel('id_group').' --']]);
        ?>
       <?= $form->field($modelForm, 'parent')->element('ui\menu\SelectTree', 
        ['items'=> $modelMenu->listItems($modelForm->id_group, $modelForm->id_menu),'settings'=>['url'=>[['run'=>'parent', 'clear'=>'ajax']]], 'options' => ['class' => 'form-control'] ]);?>
   </div>
 <?php $tabs->endTab();?>
    
<?php $tabs->beginTab(Text::_("EXP_MENU_ITEMS_PARAMS_META"));?>
     <?= $form->field($modelForm,'meta_robots')->element('ui\meta\Robots', ['options' => 
    ['class' => 'form-control', 'style'=>'width:300px; display:block;', 'prompt'=>'-- '.$modelForm->getAttributeLabel('meta_robots').' --']]);
        ?>
    <?= $form->field($modelForm, 'meta_title');?>
    <?= $form->field($modelForm, 'meta_author');?>
    <?= $form->field($modelForm, 'meta_key')->textarea();?>
    <?= $form->field($modelForm, 'meta_des')->textarea();?>
   
 <?php $tabs->endTab();?>   
 
<?php $tabs->beginTab(Text::_("EXP_MENU_ADD_ITEM_FORM_TABSTWO"));?>
    <?php $acc = JqAccordion::begin(['options'=>['id'=>'admin-accordion-groupmenu']]);?> 
    <?php if(isset($params->accordion)):?>
    <?php foreach($params->accordion as $fielset):?>
    <?php $acc->beginTab(Text::_($fielset["title"]));?>
    <div class="form-horizontal">
        <?php foreach ($fielset as $element): ?>
        <div class="form-group">
            <?= ui\help\Tooltip::element(['content'=>Text::_($element['title']), 'help'=>(isset($element['description']) ? $element['description'] : null), 'htmlOptions'=>['class'=>'col-sm-3 control-label']]);?>
            <div class="col-sm-9"><?php echo $xmlParams->elemenet($element, Html::getInputName($modelForm, 'param')) ?></div>
        </div>
        <?php endforeach;?>
    </div>
    <?php $acc->endTab();?>
    <?php endforeach;?>
    <?php endif;?>
    <?php $acc->beginTab(Text::_("EXP_MENU_ITEMS_PARAMS_VIEW"));?>
        <?=$form->field($modelForm, 'param[menu_css_class]')->label(Text::_("EXP_MENU_ITEMS_PARAMS_VIEW_ITEMSCSS"));?>
        <?=$form->field($modelForm, 'param[menu_body_class]')->label(Text::_("EXP_MENU_ITEMS_PARAMS_VIEW_BODYCSS"));?>
        <?=$form->field($modelForm, 'param[menu_attr_rel]')->label(Text::_("EXP_MENU_ITEMS_PARAMS_VIEW_ATTRREL"));?>
        <?=$form->field($modelForm, 'param[menu_attr_onclick]')->label(Text::_("EXP_MENU_ITEMS_PARAMS_VIEW_ATTRONCLICK"));?>
    <?php $acc->endTab();?>
    <?php JqAccordion::end();?>   
 <?php $tabs->endTab();?> 
    
<?php JqTabs::end();?>   
<?php
echo $form->field($modelForm, 'id_exp', ['template' => '{input}'])->hiddenInput();
echo $form->field($modelForm, 'id_menu', ['template' => '{input}'])->hiddenInput();
echo $form->field($modelForm, 'component', ['template' => '{input}'])->hiddenInput();
echo $form->field($modelForm, 'controller', ['template' => '{input}'])->hiddenInput();
echo $form->field($modelForm, 'view', ['template' => '{input}'])->hiddenInput();
echo $form->field($modelForm, 'layout', ['template' => '{input}'])->hiddenInput();

ui\form\FormBuilder::end();
?>
</div>
