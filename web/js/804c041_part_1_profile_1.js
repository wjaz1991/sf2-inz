$(function() {
    if(window.innerWidth < 992) {
        $('.sidebar-nav').addClass('nav-static');
    }
    
    $('.sidebar-nav').affix({
        offset: {
            top: 0
        }
    });
    
    $('.sidebar-nav').on('affixed.bs.affix', function() {
        $(this).addClass('col-md-3');
    });
    
    $(window).on('resize', function() {
        var element = $('.sidebar-nav');
        
        if(window.innerWidth < 992) {
            if(!element.hasClass('nav-static')) {
                element.addClass('nav-static');
            }
        } else {
            if(element.hasClass('nav-static')) {
                element.removeClass('nav-static');
            }
            
            if(!element.hasClass('affix')) {
                //element.addClass('col-md-12');
            }
        }
    });
    
});