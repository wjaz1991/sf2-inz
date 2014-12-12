$(function() {
    $('.thumbnail-image').hover(function() {
        $(this).addClass('thumb-show');
    }, function() {
        $(this).removeClass('thumb-show');
    });
});