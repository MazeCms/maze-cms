jQuery(document).ready(function () {

    new LogoLabyrinth({
        id_elem:'logo-cms',
        radius:10,
        width:4,
        ring_param: [
            {color: '#DDE7E9', speed: '1000', reverse: 1, easing: 'linear'},
            {color: '#DDE7E9', speed: '2000', reverse: -1, easing: 'bounce'},
            {color: '#DDE7E9', speed: '4000', reverse: 1, easing: 'linear'},
            {color: '#DDE7E9', speed: '5000', reverse: -1, easing: 'bounce'},
            {color: '#DDE7E9', speed: '6000', reverse: 1, easing: 'linear'}
        ]
    })
});