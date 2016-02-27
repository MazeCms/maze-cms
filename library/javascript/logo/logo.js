function LogoLabyrinth(options)
{
    this.options = $.extend({
        id_elem: 'logo',
        radius: 20,
        num_ring: 5,
        easing: 'linear', // linear|easeIn|easeOut|easeInOut|backIn|backOut|elastic|bounce
        color: '#52A02E',
        appendTo: 'body',
        width: 15,
        speed: 2000,
        gap: 10,
        repeat: 'Infinity',
        ring_param: [
            {color: '#428BCA', speed: '1000', reverse: 1, easing: 'linear'},
            {color: '#D9534F', speed: '2000', reverse: -1, easing: 'bounce'},
            {color: '#F0AD4E', speed: '4000', reverse: 1, easing: 'linear'},
            {color: '#428BCA', speed: '5000', reverse: -1, easing: 'bounce'},
            {color: '#5CB85C', speed: '6000', reverse: 1, easing: 'linear'}
        ]

    }, options || {});

    var options = this.options

    this.ring = null;

    this.anim = null;

    this.wrap = $('<div>').appendTo(options.appendTo);

    var w = h = (options.radius + (options.num_ring * options.width)) * 2;
    this.canvas = Raphael(document.getElementById(options.id_elem), w, h);

    this.createRing();

    this.setAnimate();

}

LogoLabyrinth.prototype.setAnimate = function ()
{
    if (this.ring == null)
        return false;
    var options = this.options;
    var easing, rev_d, rev, speed;
    this.anim = Array();
    for (var i = 0; i < this.ring.length; i++)
    {
        rev_d = (i + 1) % 2 ? -1 : 1
        if (i in options.ring_param)
        {
            rev = 'reverse' in options.ring_param[i] ? options.ring_param[i].reverse : rev_d;
            easing = 'easing' in options.ring_param[i] ? options.ring_param[i].easing : options.easing;
            speed = 'speed' in options.ring_param[i] ? options.ring_param[i].speed : options.speed;
        }
        else
        {
            rev = rev_d;
            easing = options.easing;
            speed = options.speed;
        }


        this.anim[i] = Raphael.animation({transform: 'r' + (360 * rev)}, speed, easing)
        this.ring[i].animate(this.anim[i])
        if (options.repeat)
        {
            this.ring[i].animate(this.anim[i].repeat(options.repeat))
        }
    }
}

LogoLabyrinth.prototype.createRing = function ()
{
    var options = this.options;
    var cx = cy = options.radius + (options.num_ring * options.width);
    var r = options.radius;
    var grad = 0;
    this.ring = Array();
    for (var i = 0; i < options.num_ring; i++)
    {
        var path = this.getPath(cx, cy, r, grad, options.gap);
        this.ring[i] = this.canvas.path(path);
        var color = i in options.ring_param ? options.ring_param[i].color : options.color;

        this.ring[i]
                .attr({'stroke': color, 'stroke-width': options.width, cursor: 'pointer'})
                .hover(function () {
                    this.animate({'stroke-width': options.width + 10}, 500, 'bounce')

                },
                        function () {
                            this.animate({'stroke-width': options.width}, 500, 'bounce')
                        })
        //.glow({opacity:0.2, color:color, width:options.width+5}) //эффект свечение
        r += options.width;
        grad += 45;
    }
}

LogoLabyrinth.prototype.getPath = function (cx, cy, r, gradus, gap)
{
    var x1, y1, x2, y2, path, grad, start, end;
    path = Array();

    for (var i = 0; i < 4; i++)
    {
        start = (Math.PI / 180) * (gradus + gap);
        end = (Math.PI / 180) * ((gradus == 0 ? 90 : gradus + 90) - gap);

        gradus = gradus == 0 ? 90 : gradus + 90;

        x1 = cx + (r * Math.cos(start));
        y1 = cy - (r * Math.sin(start));
        x2 = cx + (r * Math.cos(end));
        y2 = cy - (r * Math.sin(end));
        path.push("M " + x1 + " " + y1 + " A " + r + " " + r + " 0 0 0" + " " + x2 + " " + y2)

    }

    return  path.join(" ");
}
function LogoText(text, options)
{
    this.options = $.extend({
        id_elem: 'logo-text',
        // linear|easeIn|easeOut|easeInOut|backIn|backOut|elastic|bounce
        easing: ['linear', 'easeIn', 'easeOut', 'easeInOut', 'backIn', 'backOut', 'elastic', 'bounce'],
        color: ['#428BCA', '#D9534F', '#F0AD4E', '#428BCA', '#5CB85C'],
        animate: [{
                1: {transform: 'r45'},
                2: {transform: 'r-45'},
                3: {transform: ''}
            },
            {
                1: {transform: 'r-45'},
                2: {transform: 'r45'},
                3: {transform: ''}
            },
            {
                1: {transform: 't0, 10'},
                2: {transform: 't0, -10'},
                3: {transform: ''}
            },
            {
                1: {transform: 't0, -10'},
                2: {transform: 't0, 10'},
                3: {transform: ''}
            },
            {
                1: {transform: 's2'},
                2: {transform: 's1'},
                3: {transform: ''}
            }
        ],
        appendTo: 'body',
        size: 60,
        font: 'Painterz',
        speed: 1000,
        canvas_w: 650,
        canvas_h: 60,
        gap: 10,
        num: 2,
        repeat: 'Infinity' //Infinity = бесконечно

    }, options || {});

    var options = this.options
    var self = this;
    this.wrap = $('<div>', {id: options.id_elem}).appendTo(options.appendTo);

    this.text = text;


    this.canvas = Raphael(document.getElementById(options.id_elem), options.canvas_w, options.canvas_h);

    this.group = this.canvas.set();

    this.printText();
    this.group.animate({
        0: {opacity: "0.1"},
        1: {opacity: "1"}
    }, options.speed > 500 ? 500 * 2 : 200);

    self.setAnimate();

}
LogoText.prototype.setAnimate = function ()
{

    var count = 0, anim = null,
            options = this.options, self = this;
    this.group.forEach(function (raf) {
        if (count == options.num)
        {
            anim = Raphael.animation(self.randomArr(options.animate), options.speed, self.randomArr(options.easing))
            raf.animate(anim)
            raf.animate(anim.repeat(options.repeat))
            count = 0;
        }
        raf.hover(function () {
            raf.animate({transform: 's2'}, 500, self.randomArr(options.easing))
        },
                function () {
                    raf.animate({transform: 's1'}, 500, self.randomArr(options.easing))
                })
        count++;

    })
}
LogoText.prototype.printText = function ()
{
    var options = this.options,
            font = this.canvas.getFont(options.font),
            step = options.size / 2;
    size = 0,
            color = null;
    for (var i = 0; i < this.text.length; i++)
    {
        color = this.text[i] !== '' ? this.randomArr(options.color) : 'none';
        this.group.push(this.canvas.print(size, options.size / 2, this.text[i], font, options.size).attr({fill: color}));
        size += step;
        switch (this.text[i])
        {
            case 'i':
                size = size - (options.size / 4);
                break;
            case 'w':
                size = size + (options.size / 4);
                break;
            case 'o':
                size = size + (options.size / 6);
                break;
        }
    }
}

LogoText.prototype.randomArr = function (array)
{
    var index = Math.round(Math.random() * array.length);
    return index in array ? array[index] : array[0];
}