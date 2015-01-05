
function Kitchen()
{
    var kitchen = this;
    this.ants = new Array();
    this.moveAnt = function(){
        var i;
        for(i=0; i<10; i++) {
            kitchen.ants[i].move();
        }
        $('#scoreInfo').html("<center><b>Раздавлено:</b>" + kitchen.dead + "  <b>Сбежало:</b>" + kitchen.gone+"</center>");
        if((kitchen.gone + kitchen.dead) < 10) {
            setTimeout(kitchen.moveAnt, 25);
        }
    };
    var i;
    for(i=0; i<10; i++) {
        this.ants[i] =new Ant(i, this);
    }
    this.gone = 0;
    this.dead = 0;
    setTimeout(this.moveAnt, 25);
}

function Ant(i, k)
{
    var kitchen = k;
    this.id = i;
    this.dead = 0;
    this.distance = 0;
    this.moveTo = function(x,y) {
        this.img.css('top', y);
        this.img.css('left', x);
    };
    this.move = function() {
        if(!this.dead) {
            this.distance+=5;
            var newX = this.startX+Math.cos(this.orientation)*this.distance;
            var newY = this.startY+Math.sin(this.orientation)*this.distance;
            this.moveTo(newX,newY);
            if(newX > $(window).width()+64 || newY > $(window).height()+64 || newY < -64 || newX < -64) {
                this.dead = 1;
                kitchen.gone++;
            }
        }
    }
    this.img = $("<img/>", {
        src: "img/ant.png",
        width: 64,
        height: 64,
        id: "ant_"+i,
        click: function(){
            if(!kitchen.ants[this.id.substr(4)].dead) {
                $(this).attr('src', "img/ant2.png");
                $(this).css('z-index', -50);
                $(this).rotate(0);
                kitchen.ants[this.id.substr(4)].dead = 1;
                kitchen.dead++;
            }
        }
    }).appendTo("#kitchen");
    this.startY = Math.floor(Math.random()*($(window).height()-200))+50;
    this.startX = Math.floor(Math.random()*($(window).width()-200))+50;
    this.orientation = Math.floor(Math.random() * Math.PI * 2);
    $('#ant_'+this.id).css('position','absolute');
    this.moveTo(this.startX, this.startY);
    this.img.rotate(this.orientation/Math.PI*180-90);
}
