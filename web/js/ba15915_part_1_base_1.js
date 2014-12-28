$(function() {
    $(".nav-dropdown").hover(function() {
        $(this).find('ul').stop(true, true).slideDown();
    }, function() {
        $(this).find('ul').stop(true, true).slideUp();
    });
    
    
    $("#e1").select2();
});