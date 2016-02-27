<?php

use maze\helpers\Html;

wid\wid_slidercontents\assets\AssetCarusel::register();
$id_css = ($params->getVar("css_id") ? $params->getVar('css_id') : "slidercontents-$id");
?>
<div <?= Html::renderTagAttributes(["class" => "slidercontents-wrapp owl-carousel owl-theme " . $params->getVar("css_class"), "id" => $id_css]) ?>>
    <?php foreach ($contents as $key => $con): ?>
        <div class="item item-<?= $key + 1 ?>">
            <?php if ($con->viewField): ?>
                <div class="content-center"> 
                    <?php foreach ($con->viewField as $v): ?>
                        <?= $v->beginWrap; ?>
                        <?= $v->renderField; ?>
                        <?= $v->endWrap; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>    
    <?php endforeach; ?>
</div>
<script>
    jQuery().ready(function () {
        var timer = null,
                time = 0,
                timeDuration = 5000,
                $elem = $('#<?= $id_css ?>');

        function animatedHtml(elem) {
            var index = elem.find('.owl-pagination .active').index();
            var target = elem.find('.owl-item')[index]
            $(target).find('[data-animo-effect]').each(function () {
                var $self = $(this);

                if ($self.is('[data-animo-delay]')) {
                    var delay = $self.attr('data-animo-delay');

                    $(this).hide();
                    setTimeout(function () {
                        $self.show().animo({animation: $self.attr('data-animo-effect'), duration: $self.attr('data-animo-duration')});
                    }, Number(delay) * 1000)

                } else {
                    $(this).animo({animation: $self.attr('data-animo-effect'), duration: $self.attr('data-animo-duration')});
                }

            })
        }
        function setPositionTimer() {
            var target = $elem.find('.owl-pagination .active')
            $('#timer-carousel').stop().animate({
                left: target.position().left
            }, 300)
        }

        $('#<?= $id_css ?>').owlCarousel({
            slideSpeed: 500,
            paginationSpeed: 500,
            addClassActive: 'active-slide',
            singleItem: true,
            afterInit: function () {
                if (!timer) {
                    timer = new Timer({
                        ID: 'timer-carousel',
                        appendTo: '#<?= $id_css ?> .owl-controls',
                        color: '#fff',
                        width: 3,
                        radius: 15,
                        time: timeDuration,
                        fillBg: 'none',
                        showPause: false,
                        resetTime: false,
                        autoStart: false,
                        onStop: function () {
                            $elem.trigger('owl.next');
                        }
                    });
                }

                setPositionTimer()
                timer.start();
            },
            startDragging: function () {
                timer.pause();
                clearTimeout(time)
            },
            afterMove: function (elem) {
                setPositionTimer();
                animatedHtml(elem);

                time = setTimeout(function () {
                    timer.reset();
                    timer.state = 1;
                    timer.start();
                }, 400)

            }
        });
        animatedHtml($('#<?= $id_css ?>'));
        $(window).resize(setPositionTimer)
    })
</script>

