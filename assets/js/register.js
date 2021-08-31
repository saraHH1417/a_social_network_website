$(document).ready(function() {
    // On clicking signup hide login show registration
    $("#signup").click(function() {
        $("#first").slideUp("slow" , function () {
            $("#second").slideDown("slow");
        });
    });
    // On clicking sign in hide registration and show login
    $("#signin").click(function() {
        $("#second").slideUp("slow" , function () {
            $("#first").slideDown("slow");
        });
    });
})