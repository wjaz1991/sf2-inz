$(function() {
    $(".nav-dropdown").hover(function() {
        $(this).find('ul').stop(true, true).show();
    }, function() {
        $(this).find('ul').stop(true, true).hide();
    });
});