/* ------------------------------------------------------------------------------
 *
 *  # Login pages
 *
 *  Demo JS code for a set of login and registration pages
 *
 * ---------------------------------------------------------------------------- */


// Setup module
// ------------------------------
var base_url = $("meta[name=base_url]").attr("content");
var user_id = $("meta[name=user_id]").attr("content");
var csrf_token = $("meta[name=csrf-token]").attr("content");
var ip_address = $("meta[name=ip_address]").attr("content");
var LoginRegistration = function () {


    //
    // Setup module components
    //

    // Uniform
    var _componentUniform = function() {
        if (!$().uniform) {
            console.warn('Warning - uniform.min.js is not loaded.');
            return;
        }

        // Initialize
        $('.form-input-styled').uniform();
    };


    //
    // Return objects assigned to module
    //

    return {
        initComponents: function() {
            _componentUniform();
        }
    }
}();


// Initialize module
// ------------------------------

document.addEventListener('DOMContentLoaded', function() {
    LoginRegistration.initComponents();
});

$(document).ready(function(){
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition,showError);

    } else {
        alert("Geolocation is not supported by this browser.");
    }

})


function showPosition(position) {

    var lat = position.coords.latitude
    var long = position.coords.longitude

    $("#latitude_in").val(lat);
    $("#longitude_in").val(long);


}


function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("Error..! Allow your location.");
           // window.location.replace(base_url+"/logout");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Error..! Location information is unavailable.");
            window.location.replace(base_url+"/logout");
            break;
        case error.TIMEOUT:
            alert("Error..! The request to get user location timed out.");
            window.location.replace(base_url+"/logout");
            break;
        case error.UNKNOWN_ERROR:
            alert("Error..! An unknown error occurred.");
            window.location.replace(base_url+"/logout");
            break;
    }
}

