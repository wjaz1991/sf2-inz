$(function() {
    var mousePos = { x: -1, y: -1 };
    
    $(document).mousemove(function(event) {
        mousePos.x = event.pageX;
        mousePos.y = event.pageY;
    });
    
    $('body').find('.full-image').css('display', 'none');
    
    $('.thumbnail-image').mouseover(function(event) {
        //console.log(event.pageX);
        //console.log(event.pageY);
        $(this).parent().find('.full-image')
                .css({
                    'position': 'absolute',
                    'height': '300px',
                    'width': 'auto',
                    'z-index': 999
                })
                .css({
                    left: event.pageX,
                    top: event.pageY - $('.thumbnail-image').offset().top -100
                })
                .fadeIn();
                console.log(event.pageX + ', ' + (event.pageY - $('.thumbnail-image').offset().top));
    });
    $('.thumbnail-image').mouseleave(function() {
        $(this).parent().find('.full-image').hide().css({
                    left: '0px',
                    top: '0px'
                });
    });
});