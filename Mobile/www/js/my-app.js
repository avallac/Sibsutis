// Initialize your app
var myApp = new Framework7();
var hi, watchID;
var ants = new Array();
var dead,gone;

// Export selectors engine
var $$ = Dom7;

// Add view
var mainView = myApp.addView('.view-main', {
    // Because we use fixed-through navbar we can enable dynamic navbar
    dynamicNavbar: true
});

myApp.onPageInit('compass', function(){
    hi = document.getElementById('headingInfo');
    var watchOptions = {	filter : 5 };
    watchID = navigator.compass.watchHeading(onHeadingSuccess, onHeadingError, watchOptions);
});

myApp.onPageInit('ant', function(){
    var i;
    for(i=0; i<10; i++) {
        ants[i] =new Ant(i);
    }
    gone = 0;
    dead = 0;
    setTimeout(moveAnt, 25);
});

function moveAnt ()
{
    for(i=0; i<10; i++) {
        ants[i].move();
    }
    $('#scoreInfo').html("<center><b>Раздавлено:</b>" + dead + "  <b>Сбежало:</b>" + gone+"</center>");
    if((gone + dead) < 10) {
        setTimeout(moveAnt, 25);
    }
}


function onHeadingSuccess(heading)
{
    var hv = Math.round(heading.magneticHeading);
    hi.innerHTML = "<b>Направление:</b>" + hv + " градусов";
    $("#compass").rotate(-hv);
}

function onHeadingError(compassError)
{
    navigator.compass.clearWatchFilter(watchID);
    if(compassError.code == CompassError.COMPASS_NOT_SUPPORTED) {
        alert("Compass not supported.");
    } else if(compassError.code == CompassError.COMPASS_INTERNAL_ERR) {
        alert("Compass Internal Error");
    } else {
        alert("Unknown heading error!");
    }
}

function calcAppend(num)
{
    $('#calcText').append(num);
}

function Ant(i)
{
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
                gone++;
            }

        }
    }
    this.img = $("<img/>", {
        src: "img/ant.png",
        width: 64,
        height: 64,
        id: "ant_"+i,
        click: function(){
             if(!ants[this.id.substr(4)].dead) {
                 $(this).attr('src', "img/ant2.png");
                 $(this).css('z-index', -50);
                 $(this).rotate(0);
                 ants[this.id.substr(4)].dead = 1;
                 dead++;
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


