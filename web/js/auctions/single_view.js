$(function() {
    //if there is a div with the images, initialize gallery
    if($(document).find('.photoset').length) {
        $('.photoset').photosetGrid({
            highresLinks: true,
            gutter: '2px',
            onInit: function(){},
            onComplete: function() {
                var title = $('.auction-title').text();
                $('.photoset').find('a').attr('data-lightbox', 'auction');
                $('.photoset').find('a').attr('data-title', title);
                $('.photoset').find('a').css('display', 'inline-block');
                $('.photoset').attr('style', '');
            }
        });
    }
});