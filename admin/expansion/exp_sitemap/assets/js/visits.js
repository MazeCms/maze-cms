jQuery(document).ready(function () {
    Highcharts.setOptions({
        lang: {
            months: ['Январь', 'Февраль', 'март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            shortMonths: ['Янв', 'Фев', 'мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота']
        }
    });

    function getConfig(data) {
        return {
            credits: {
                enabled: false
            },
            chart: {
                type: 'area',
                zoomType: 'x'
            },
            title: {
                text: ''
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                        'Нажмите и перетащите область диаграмы, чтобы увеличить' :
                        'Зажмите диаграмму, чтобы увеличить'
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    overflow: 'justify'
                }
            },
            yAxis: {
                title: {
                    text: 'Количество посещений'
                }

            },
            tooltip: {
                valueSuffix: ' робот'
            },
            plotOptions: {
                area: {
                    marker: {
                        enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                            hover: {
                                enabled: true
                            }
                        }
                    }
                }
            },
            series: data,
            navigation: {
                menuItemStyle: {
                    fontSize: '10px'
                }
            }
        }
    }

    $.get(cms.getURL([{run: 'robots', type: 'xml', clear: 'ajax'}]), function (data) {
        $.each(data.html, function (i, obj) {
            $.each(obj.data, function (k, val) {
                data.html[i].data[k].x = Date.parse(val.x);
            })
        })
        $('#robots-type-xml').highcharts(getConfig(data.html));
    }, 'json');

    $.get(cms.getURL([{run: 'robots', type: 'html', clear: 'ajax'}]), function (data) {
        $.each(data.html, function (i, obj) {
            $.each(obj.data, function (k, val) {
                data.html[i].data[k].x = Date.parse(val.x);
            })
        })
        $('#robots-type-html').highcharts(getConfig(data.html));
    }, 'json');

    function loadTypeRobots(id, type) {
        var block = $('#' + id);
        var params = {
            run: 'mapRobots', 
            type:type,
            sitemap_id:block.find('[name=sitemap_id]').val(),
            in_date_visits:block.find('[name=in_date_visits]').val(),
            out_date_visits:block.find('[name=out_date_visits]').val(),
            clear: 'ajax'
        };
        $.get(cms.getURL([params]), function (data) {
            $('#' + id).find('.body-map').highcharts({
                credits: {
                    enabled: false
                },
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Количестов посещений'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span>{point.name}</span>: <b>{point.y} </b><br/>'
                },
                series: [{
                        name: "Робот",
                        colorByPoint: true,
                        data: data.html
                    }]
            });

        }, 'json');
    }
    
   
    loadTypeRobots('type-map-robots-html','html')
    loadTypeRobots('type-map-robots-xlm','xml')
    $('#type-map-robots-xlm').change(function(){
        loadTypeRobots('type-map-robots-xlm','xml')
    })
    $('#type-map-robots-html').change(function(){
        loadTypeRobots('type-map-robots-html','html')
    })
})