// Initialize your app
var myApp = new Framework7();
var hi, watchID;

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

function onHeadingSuccess(heading) {
        //alert("onHeadingSuccess");
        var hv = Math.round(heading.magneticHeading);
        hi.innerHTML = "<b>Направление:</b>" + hv + " градусов";
        $("#compass").rotate(-hv);
}

function onHeadingError(compassError) {
        //Remove the watch since we're having a problem
        navigator.compass.clearWatchFilter(watchID);
        //Then tell the user what happened.
        if(compassError.code == CompassError.COMPASS_NOT_SUPPORTED) {
                alert("Compass not supported.");
        } else if(compassError.code == CompassError.COMPASS_INTERNAL_ERR) {
                alert("Compass Internal Error");
        } else {
                alert("Unknown heading error!");
        }
}