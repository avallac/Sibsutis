var myApp = new Framework7();
var watchID;
var $$ = Dom7;

myApp.addView('.view-main', {
    dynamicNavbar: true
});

myApp.onPageInit('compass', function(){
    watchID = navigator.compass.watchHeading(onHeadingSuccess, onHeadingError, { filter : 5 });
});

myApp.onPageInit('ant', function(){
    new Kitchen();
});

myApp.onPageInit('calc', function() {
    new Calc();
});

function onHeadingSuccess(heading)
{
    var hv = Math.round(heading.magneticHeading);
    $('#headingInfo').html("<b>Направление:</b>" + hv + " градусов");
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




