jQuery(document).ready(function () {
    Highcharts.setOptions({
	lang: {
		months: ['Январь', 'Февраль', 'март', 'Апрель', 'Май', 'Июнь',  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                shortMonths:['Янв', 'Фев', 'мар', 'Апр', 'Май', 'Июн',  'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
		weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота']
	}
    });
    
    $.get(cms.getURL([{run: 'filesize', clear:'ajax'}]), function (data) {
        $('#log-file-size').highcharts({
            credits:{
                enabled:false
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
                    text: cms.getLang('EXP_LOGS_CHART_FILESIZE_TITLEY')
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f} '+cms.getLang('EXP_LOGS_CHART_FILESIZE_BYITE')
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f} '+cms.getLang('EXP_LOGS_CHART_FILESIZE_BYITE')+'</b><br/>'
            },
            series: [{
                    name: cms.getLang('EXP_LOGS_CHART_FILESIZE_TOTLTIPSIZE'),
                    colorByPoint: true,
                    data: data.html
                }]
        });

    }, 'json');
    
    $.get(cms.getURL([{run: 'requestApp', clear:'ajax'}]), function (data) {
        $.each(data.html, function(i, obj){  
            $.each(obj.data,function(k, val){
                data.html[i].data[k].x = Date.parse(val.x);  
            })
        })
        
    $('#log-type-request').highcharts({
        credits:{
                enabled:false
            },
        chart: {
            type: 'spline',
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
                text: 'Код состояния ответа'
            },
            min: 0,
            minorGridLineWidth: 0,
            gridLineWidth: 0,
            alternateGridColor: null,
            plotBands: [{ 
                from: 100,
                to: 118,
                color: 'rgba(68, 170, 213, 0.1)',
                label: {
                    text: 'Connection timed out',
                    style: {
                        color: '#606060'
                    }
                }
            }, {
                from: 200,
                to: 200,
                color: 'rgba(0, 0, 0, 0)',
                label: {
                    text: 'OK',
                    style: {
                        color: '#606060'
                    }
                }
            }, { 
                from: 301,
                to: 302,
                color: 'rgba(68, 170, 213, 0.1)',
                label: {
                    text: 'Redirect',
                    style: {
                        color: '#606060'
                    }
                }
            }, { 
                from: 400,
                to: 450,
                color: 'rgba(0, 0, 0, 0)',
                label: {
                    text: 'Error page',
                    style: {
                        color: '#606060'
                    }
                }
            }, { 
                from: 500,
                to: 600,
                color: 'rgba(68, 170, 213, 0.1)',
                label: {
                    text: 'Error server',
                    style: {
                        color: '#606060'
                    }
                }
            }]
        },
        tooltip: {
            valueSuffix: ' компаннента'
        },
        series: data.html,
        navigation: {
            menuItemStyle: {
                fontSize: '10px'
            }
        }
    });
    }, 'json');
    
    $.get(cms.getURL([{run: 'userExp', clear:'ajax'}]), function (data) {
    $('#log-type-exp').highcharts({
        credits:{
                enabled:false
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Пользователь',
            data: data.html
        }]
    });
     }, 'json');
     
    $.get(cms.getURL([{run: 'errorPocent', clear:'ajax'}]), function (data) {
    $('#log-type-error').highcharts({
        credits:{
                enabled:false
        },
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Пользователь',
            data: data.html
        }]
    });
     }, 'json');
     
    $.get(cms.getURL([{run: 'dbCount', clear:'ajax'}]), function (data) {
        
     
        $.each(data.html, function(i, val){           
           data.html[i].x = Date.parse(val.x);            
        })

    $('#log-type-dbcount').highcharts({
        credits:{
                enabled:false
        },
         chart: {
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
        },
        yAxis: {
            title: {
                text: 'Количество запросов'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        series: [{
            type: 'area',
            name: 'Количество запросов ',
            data: data.html
        }]
    });
     }, 'json');
})