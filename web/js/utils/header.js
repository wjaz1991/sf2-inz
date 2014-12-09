$(function() {
    //nav animation on scrolling
    $(window).on('scroll', function() {
        var distanceY = window.pageYOffset || document.documentElement.scrollTop;
        var scrollLimit = 300;

        if(distanceY > scrollLimit) {
            $('header').addClass('smaller');
            $('#content-wrap').addClass('smaller');
        } else {
            if($('header').hasClass('smaller')) {
                $('header').removeClass('smaller');
                $('#content-wrap').removeClass('smaller');
            }
        }
    });
    
    $(".nav-dropdown").hover(function() {
        $(this).find('ul').stop(true, true).show();
    }, function() {
        $(this).find('ul').stop(true, true).hide();
    });
});