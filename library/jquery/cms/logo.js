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

    this.wrap = $('<div>', {id: options.id_elem}).appendTo(options.appendTo);

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
